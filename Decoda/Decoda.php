<?php

namespace FM\BbcodeBundle\Decoda;

use Decoda as BaseDecoda;

class Decoda extends BaseDecoda
{
    const DECODA_MESSAGES_FILE_PATH = '/../Resources/config/messages.json';

    /**
     * {@inheritDoc}
     */
    public function __construct($string = '', $messages = array())
    {
        spl_autoload_register(array($this, 'loadFile'));

        $this->setMessages($messages);
        $this->reset($string, true);
    }

    /**
     * Autoload filters and hooks.
     *
     * @access public
     * @param  string $class
     * @return void
     */
    public function loadFile($class)
    {
        if (class_exists($class) || interface_exists($class)) {
            return;
        }

        if (strpos($class, 'Filter') !== false && file_exists(DECODA_FILTERS . $class . '.php') ) {
            include_once DECODA_FILTERS . $class . '.php';
        } elseif (strpos($class, 'Hook') !== false && file_exists(DECODA_HOOKS . $class . '.php') ) {
            include_once DECODA_HOOKS . $class . '.php';
        }
    }

    /**
     * Set messages
     *
     * @param array $messages
     */
    public function setMessages($messages = array())
    {
        if (empty($messages)) {
            $this->_messages = json_decode(\file_get_contents(__DIR__ . self::DECODA_MESSAGES_FILE_PATH), true);
        } else {
            $this->_messages = $messages;
        }
    }
}
