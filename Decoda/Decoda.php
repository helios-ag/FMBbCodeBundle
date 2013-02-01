<?php
namespace FM\BbcodeBundle\Decoda;

define('DECODA', __DIR__.'/../../../../../mjohnson/decoda/src/Decoda');
define('DECODA_FILTERS', DECODA.'/Filter');
define('DECODA_HOOKS', DECODA.'/Hook');

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


    public function setLocale($locale) {
        $this->message(null);
        if(strlen($locale)<3)
            foreach($this->_messages as $key => $value){
                $this->_messages[substr($key, 0, 2)] = $value;
                unset($this->_messages[$key]);
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
            $this->_messages = json_decode(\file_get_contents(DECODA.'/config/messages.json'), true);
        } else {
            $this->_messages = $messages;
        }
    }
}
