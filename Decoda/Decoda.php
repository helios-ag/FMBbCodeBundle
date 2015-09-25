<?php

namespace FM\BbcodeBundle\Decoda;

use Decoda\Loader\DataLoader;
use Decoda\Loader;
use Decoda\Hook;
use Decoda\Filter;
use Decoda\Decoda as BaseDecoda;
use OutOfRangeException;

/**
 * Class Decoda.
 */
class Decoda extends BaseDecoda
{
    /**
     * @var string
     */
    protected $defaultLocale;

    /**
     * Store the text and single instance configuration.
     *
     * @param string $string
     * @param array  $config
     */
    public function __construct($string = '', array $config = array())
    {
        parent::__construct($string, $config);

        $this->_loadMessages();
    }

    /**
     * Set the locale.
     *
     * @param string $locale
     *
     * @return Decoda
     *
     * @throws DomainException
     */
    public function setLocale($locale)
    {
        if (false !== strpos($locale, '-')) {
            $locales = explode('-', $locale);
            $locale  = $locales[0];
        }

        return parent::setLocale($locale);
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
            $locale  = $locales[0];
        }

        $this->defaultLocale = $locale;

        if (null === $this->getConfig('locale')) {
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
        try {
            $translated = parent::message($key, $vars);
        } catch (OutOfRangeException $e) {
            if ($this->defaultLocale === null) {
                throw $e;
            }

            // fallback default locale
            $locale = $this->getConfig('locale');
            $this->setLocale($this->defaultLocale);

            $translated = parent::message($key, $vars);

            $this->setLocale($locale);
        }

        return $translated;
    }

    /**
     * Set messages.
     *
     * @param array $messages
     */
    public function setMessages(array $messages = array())
    {
        $this->_messages = array();
        $this->addMessages(new DataLoader($messages));
    }

    /**
     * Add a loader that will generate localization messages.
     *
     * @param \Decoda\Loader $loader
     *
     * @return \Decoda\Decoda
     */
    public function addMessages(Loader $loader)
    {
        $loader->setParser($this);

        if ($messages = $loader->load()) {
            foreach ($messages as $locale => $strings) {
                foreach ($strings as $id => $message) {
                    $this->setMessage($locale, $id, $message);
                }
            }
        }

        return $this;
    }

    /**
     * Sets a message translation.
     *
     * @param string $locale      The locale
     * @param string $id          The message id
     * @param string $translation The messages translation
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
        if (null === $id) {
            // this is to keep method signature
            $id = explode('\\', get_class($filter));
            $id = str_replace('Filter', '', end($id));
        }

        return parent::addFilter($filter, strtolower($id));
    }

    /**
     * Check if a filter exists.
     *
     * @see \Decoda\Decoda::hasFilter()
     *
     * @param string $id
     *
     * @return bool
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
     *
     * @throws InvalidArgumentException
     *
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
     *
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
        if (null === $id) {
            // this is to keep method signature
            $id = explode('\\', get_class($hook));
            $id = str_replace('Filter', '', end($id));
        }

        return parent::addHook($hook, strtolower($id));
    }

    /**
     * Check if a hook exists.
     *
     * @see \Decoda\Decoda::hasHook()
     *
     * @param string $id
     *
     * @return bool
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
     *
     * @throws InvalidArgumentException
     *
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
     *
     * @return \Decoda\Decoda
     */
    public function removeHook($ids)
    {
        return parent::removeHook(array_map('strtolower', (array) $ids));
    }

    /**
     * {@inheritdoc}
     */
    protected function _extractChunks($string)
    {
        if (isset($this->_filters['list'])) {
            $this->_filters['List'] = true;
        }

        $nodes = parent::_extractChunks($string);

        unset($this->_filters['List']);

        return $nodes;
    }
}
