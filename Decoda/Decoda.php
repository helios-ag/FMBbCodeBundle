<?php

namespace FM\BbcodeBundle\Decoda;

use Decoda\Hook;
use Decoda\Filter;
use Decoda\Decoda as BaseDecoda;
use \DomainException;

/**
 * Class Decoda
 * @package FM\BbcodeBundle\Decoda
 */
class Decoda extends BaseDecoda
{
    /**
     * @var string
     */
    protected $defaultLocale;


    /**
     * @param string $string    The string to parse
     * @param array  $messages  An array of messages translation
     */
    public function __construct($string = '', array $messages = array())
    {
        parent::__construct($string);

        // Force the generation of default Decoda messages
        $this->message(null);

        $this->setMessages(array_merge($this->_messages, $messages));
    }

    /**
     * Set the locale.
     *
     * @param string $locale
     * @return Decoda
     * @throws DomainException
     */
    public function setLocale($locale)
    {
        if (false !== strpos($locale, '-')) {
            $locales = explode('-', $locale);
            $locale = $locales[0];
        }

        try {
            parent::setLocale($locale);
        } catch (\DomainException $e) {
            if (null !== $this->defaultLocale) {
                parent::setLocale($this->defaultLocale);
            } else {
                throw $e;
            }
        }

        return $this;
    }

    /**
     * Set the default locale.
     *
     * @param string $defaultLocale
     *
     * @return Decoda
     *
     * @throws DomainException
     */
    public function setDefaultLocale($locale)
    {
        if (false !== strpos($locale, '-')) {
            $locales = explode('-', $locale);
            $locale = $locales[0];
        }

        if (!isset($this->_messages[$locale])) {
            throw new DomainException(sprintf('Localized strings for %s do not exist', $locale));
        }

        $this->defaultLocale = $locale;

        if (null === $this->config('locale')) {
            parent::setLocale($locale);
        }

        return $this;
    }

    /**
     * Return a message string if it exists.
     *
     * @param string $key
     * @param array  $vars
     *
     * @return string
     */
    public function message($key, array $vars = array())
    {
        $translated = parent::message($key, $vars);

        if (!empty($key) && $this->defaultLocale !== null && empty($translated)) {
            // fallback default locale
            $locale = $this->config('locale');
            parent::setLocale($this->defaultLocale);

            $translated = parent::message($key, $vars);

            parent::setLocale($locale);
        }

        return $translated;
    }

    /**
     * Set messages
     *
     * @param array $messages
     */
    public function setMessages(array $messages = array())
    {
        $this->_messages = array();
        $this->addMessages($messages);
    }

    /**
     * Adds messages to the parser messeges.
     *
     * @param array $messages An array of messages with keys are locales
     */
    public function addMessages(array $messages)
    {
        foreach ($messages as $locale => $value){
            foreach ($value as $id => $message){
                $this->setMessage($locale, $id, $message);
            }
        }
    }

    /**
     * Sets a message translation.
     *
     * @param string $locale        The locale
     * @param string $id            The message id
     * @param string $translation   The messages translation
     */
    public function setMessage($locale, $id, $translation)
    {
        if (false !== strpos($locale, '-')) {
            $locales = explode('-', $locale);
        } else {
            $locales = array($locale);
        }

        foreach ($locales as $loc) {
            $this->_messages[$loc][$id] = $translation;
        }
    }

    /**
     * @return array
     */
    public function getWriteList()
    {
        return $this->_whitelist;
    }

    /**
     * @return array
     */
    public function getBlackList()
    {
        return $this->_blacklist;
    }

    /**
     * Add a configuration lookup path.
     *
     * @param string $path
     * @return \Decoda\Decoda
     */
    public function addPath($path)
    {
        if (substr($path, -1) !== '/') {
            $path .= '/';
        }

        return parent::addPath($path);
    }


    /**
     * Add additional filters.
     *
     * @see \Decoda\Decoda::addFilter()
     *
     * @param Filter $filter
     * @param string $id
     *
     * @return Decoda
     */
    public function addFilter(Filter $filter, $id = null)
    {
        $filter->setParser($this);

        if (null === $id) {
            // this is to keep method signature
            $id = explode('\\', get_class($filter));
            $id = str_replace('Filter', '', end($id));
        }

        $id = strtolower($id);

        $tags = $filter->tags();

        $this->_filters[$id] = $filter;

        $this->_tags = $tags + $this->_tags;

        foreach ($tags as $tag => $options) {
            $this->_filterMap[$tag] = $id;
        }

        $filter->setupHooks($this);

        return $this;
    }

    /**
     * Check if a filter exists.
     *
     * @see \Decoda\Decoda::hasFilter()
     *
     * @param string $id
     * @return boolean
     */
    public function hasFilter($id)
    {
        return parent::hasFilter(strtolower($id));
    }

    /**
     * Return a specific filter based on filter id.
     *
     * @see \Decoda\Decoda::getFilter()
     *
     * @param string $id
     * @throws InvalidArgumentException
     * @return \Decoda\Filter[]
     */
    public function getFilter($id)
    {
        return parent::getFilter(strtolower($id));
    }

    /**
     * Remove filter(s).
     *
     * @param string|array $filters
     * @return \Decoda\Decoda
     */
    public function removeFilter($ids)
    {
        return parent::removeFilter(array_map('strtolower', (array) $ids));
    }

    /**
     * Add hooks that are triggered at specific events.
     *
     * @see \Decoda\Decoda::addHook()
     *
     * @param Hook   $hook
     * @param string $id
     *
     * @return Decoda
     */
    public function addHook(Hook $hook, $id = null)
    {
        $hook->setParser($this);

        if (null === $id) {
            // this is to keep method signature
            $id = explode('\\', get_class($hook));
            $id = str_replace('Filter', '', end($id));
        }

        $this->_hooks[strtolower($id)] = $hook;

        $hook->setupFilters($this);

        return $this;
    }


    /**
     * Check if a hook exists.
     *
     * @see \Decoda\Decoda::hasHook()
     *
     * @param string $id
     * @return boolean
     */
    public function hasHook($id)
    {
        return parent::hasHook(strtolower($id));
    }

    /**
     * Return a specific hook based on hook id.
     *
     * @see \Decoda\Decoda::getHook()
     *
     * @param string $id
     * @throws InvalidArgumentException
     * @return \Decoda\Hook[]
     */
    public function getHook($id)
    {
        return parent::getHook(strtolower($id));
    }

    /**
     * Remove hook(s).
     *
     * @param string|array $hooks
     * @return \Decoda\Decoda
     */
    public function removeHook($ids)
    {
        return parent::removeHook(array_map('strtolower', (array) $ids));
    }
}
