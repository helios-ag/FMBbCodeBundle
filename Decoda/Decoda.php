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

        parent::setLocale($locale);
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
}
