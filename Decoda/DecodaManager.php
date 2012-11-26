<?php
namespace FM\BbcodeBundle\Decoda;

use FM\BbcodeBundle\Decoda\Decoda;
use FM\BbcodeBundle\Decoda\DecodaPhpEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use mjohnson\decoda\filters\Filter,
    mjohnson\decoda\filters\DefaultFilter,
    mjohnson\decoda\filters\BlockFilter,
    mjohnson\decoda\filters\CodeFilter,
    mjohnson\decoda\filters\EmailFilter,
    mjohnson\decoda\filters\ImageFilter,
    mjohnson\decoda\filters\ListFilter,
    mjohnson\decoda\filters\QuoteFilter,
    mjohnson\decoda\filters\TextFilter,
    mjohnson\decoda\filters\UrlFilter,
    mjohnson\decoda\filters\VideoFilter;
use mjohnson\decoda\hooks\CensorHook,
    mjohnson\decoda\hooks\ClickableHook,
    mjohnson\decoda\hooks\CodeHook,
    mjohnson\decoda\hooks\EmoticonHook,
    mjohnson\decoda\hooks\Hook;

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

    protected static $extra_filters = array();
    protected static $extra_hooks = array();
    protected static $extra_paths = array();

    /**
     * @param Decoda $value
     * @param array $filters
     * @param array $hooks
     * @param array $whitelist
     */
    public function __construct(Decoda $value, array $filters = array(), array $hooks = array(), array $whitelist = array())
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

    public static function add_templatePath( $path ){
        static::$extra_paths[] = $path;
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
        if(isset(static::$extra_filters[$filter])){
            $extra_filter = static::$extra_filters[$filter] instanceof Filter ? static::$extra_filters[$filter] : new static::$extra_filters[$filter]();
            $code->addFilter($extra_filter);
            return $code;
        }

        switch ($filter) {
            case 'block':
                $code->addFilter(new BlockFilter());
                break;
            case 'code':
                $code->addFilter(new CodeFilter());
                break;
            case 'email':
                $code->addFilter(new EmailFilter());
                break;
            case 'image':
                $code->addFilter(new ImageFilter());
                break;
            case 'list':
                $code->addFilter(new ListFilter());
                break;
            case 'quote':
                $code->addFilter(new QuoteFilter());
                break;
            case 'text':
                $code->addFilter(new TextFilter());
                break;
            case 'url':
                $code->addFilter(new UrlFilter());
                break;
            case 'video':
                $code->addFilter(new VideoFilter());
                break;
            case 'default':
                $code->addFilter(new DefaultFilter());
                break;
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
            $extra_hook = static::$extra_hooks[$hook] instanceof Hook ? static::$extra_hooks[$hook] : new static::$extra_hooks[$hook]();
            $code->addFilter($extra_hook);
            return $code;
        }

        switch ($hook) {
            case 'censor':
                $code->addHook(new CensorHook());
                break;
            case 'clickable':
                $code->addHook(new ClickableHook());
                break;
            case 'emoticon':
                $code->addHook(new EmoticonHook());
                break;
            case 'code':
                $code->addHook(new CodeHook());
                break;
        }
        return $code;
    }

    /**
     * @param Decoda $code
     * @param array $whitelist
     * @return Decoda
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
        $decodaPhpEngine = new DecodaPhpEngine();

        foreach(static::$extra_paths as $extraPath){
            $decodaPhpEngine->setpath($extraPath);
        }

        $this->value->setEngine($decodaPhpEngine);


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
