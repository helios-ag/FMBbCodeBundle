<?php

namespace FM\BbCodeBundle\Decoda;

use Decoda as BaseDecoda;

class Decoda extends BaseDecoda{

    const DECODA_MESSAGES_FILE_PATH = '/../Resources/config/messages.json';
    
    /**
     * {@inheritDoc}
     */
	public function __construct($string = '', $messages = array()) {
		spl_autoload_register(array($this, 'loadFile'));
		
		$this->setMessages($messages);
		$this->reset($string, true);
	}
    
    /**
     * Set messages
     * 
     * @param array $messages 
     */
    public function setMessages($messages = array()) {
        
        if (empty($messages)) {
            $this->_messages = json_decode(\file_get_contents(__DIR__ . self::DECODA_MESSAGES_FILE_PATH), true);
        } else {
            $this->_messages = $messages;
        }
    }
}