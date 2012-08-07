<?php

namespace FM\BbcodeBundle\Decoda;

use FM\BbcodeBundle\Decoda\Decoda;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2012 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class DecodaManager
{
    protected $filters;
    protected $hooks;
    protected $whitelist;
    protected $value;

    protected static $extra_filters;
    protected static $extra_hooks;
    /**
     * @param Decoda $value
     * @param array $filters
     * @param array $hooks
     * @param array $whitelist
     */
    public function __construct(Decoda $value, array $filters = array(), array $hooks=array(), array $whitelist = array())
    {
        $this->value = $value;
        $this->filters = $filters;
        $this->hooks = $hooks;
        $this->whitelist = $whitelist;

    }

    public static function add_filter($name, $filter){
        static::$extra_filters[$name] = $filter;
    }


    public static function add_hook($name, $hook){
        static::$extra_hooks[$name] = $hook;
    }


    /**
     * Applies filter specified in parameter
     * @param \FM\BbcodeBundle\Decoda\Decoda $code
     * @param string $filter
     *
     * @return \FM\BbcodeBundle\Decoda\Decoda
     */
    protected function apply_filter(Decoda $code, $filter)
    {
        //default, block, code, email, image, list, quote, text, url, video ]
        if(isset(static::$extra_filters[$filter])){
            $code->addFilter(new static::$extra_filters[$filter]());
            return $code;
        }

        switch ($filter) {
            case 'block':
                $code->addFilter(new \BlockFilter());
                break;
            case 'code':
                $code->addFilter(new \CodeFilter());
                break;
            case 'email':
                $code->addFilter(new \EmailFilter());
                break;
            case 'image':
                $code->addFilter(new \ImageFilter());
                break;
            case 'list':
                $code->addFilter(new \ListFilter());
                break;
            case 'quote':
                $code->addFilter(new \QuoteFilter());
                break;
            case 'text':
                $code->addFilter(new \TextFilter());
                break;
            case 'url':
                $code->addFilter(new \UrlFilter());
                break;
            case 'video':
                $code->addFilter(new \VideoFilter());
                break;
            case 'default':
                $code->addFilter(new \DefaultFilter());
            default:
                return $code;
        }

        return $code;
    }
    /**
     * @param Decoda $code
     * @param $hook
     * @return Decoda
     */
    protected function apply_hook(Decoda $code, $hook)
    {
        if(isset(static::$extra_hooks[$hook])){
            $code->addFilter(new static::$extra_hooks[$hook]());
            return $code;
        }

        switch ($hook) {
            case 'censor':
                $code->addHook(new \CensorHook());
                break;
            case 'clickable':
                $code->addHook(new \ClickableHook());
                break;
            case 'emoticon':
                $code->addHook(new \EmoticonHook());
                break;
        }
        return $code;
    }

    /**
     * @param Decoda $code
     * @param array $whitelist
     * @return \Decoda
     */
    protected function apply_whitelist(Decoda $code, array $whitelist)
    {
        return $code->whitelist($whitelist);
    }

    /**
     *
     * @return Decoda
     */
    public function getResult()
    {
        foreach($this->filters as $filter)
        {
            $this->value = $this->apply_filter($this->value, $filter);
        }

        foreach($this->hooks as $hook)
        {
            $this->value = $this->apply_hook($this->value, $hook);
        }

        $this->value = $this->apply_whitelist($this->value, $this->whitelist);

        return $this->value;
    }
}