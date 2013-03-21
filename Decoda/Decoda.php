<?php
namespace FM\BbcodeBundle\Decoda;

define('DECODA', __DIR__.'/../../../../../mjohnson/decoda/src/Decoda');

use Decoda\Decoda as BaseDecoda;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \DomainException;

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

    public function setLocale($locale) {
        $this->message(null);

        if(strlen($locale)<3)
            foreach($this->_messages as $key => $value){
                if(strlen($key)>2){
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
        if (empty($messages)) {
            $this->_messages = json_decode(\file_get_contents(__DIR__ . '/../Resources/config/messages.json'), true);
        } else {
            $this->_messages = $messages;
        }
    }
}
