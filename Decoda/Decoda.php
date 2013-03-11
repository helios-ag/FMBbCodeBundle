<?php

namespace FM\BbcodeBundle\Decoda;

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
}
