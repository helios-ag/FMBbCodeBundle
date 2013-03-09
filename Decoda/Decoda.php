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
     * {@inheritDoc}
     */
    public function __construct($string = '', $messages = array())
    {
        $this->setMessages($messages);
        $this->reset($string, true);
    }

    /**
     * @param string $locale
     * @return $this|BaseDecoda
     * @throws \DomainException
     */
    public function setLocale($locale)
    {
        $this->message(null);

        if(strlen($locale)<3)
            foreach ($this->_messages as $key => $value) {
                if (strlen($key)>2) {
                    $this->_messages[substr($key, 0, 2)] = $value;
                    unset($this->_messages[$key]);
                }
            }

        if (empty($this->_messages[$locale])) {
            throw new DomainException(sprintf('Localized strings for %s do not exist', $locale));
        }

        $this->_config['locale'] = $locale;

        return $this;
    }

    /**
     * Set messages
     *
     * @param array $messages
     */
    public function setMessages($messages = array())
    {
        $this->_messages = $messages;
    }

}
