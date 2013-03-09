<?php
namespace FM\BbcodeBundle\Decoda;

use FM\BbcodeBundle\Decoda\Decoda;
use FM\BbcodeBundle\Decoda\DecodaPhpEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Decoda\Filter,
    Decoda\Filter\DefaultFilter,
    Decoda\Filter\BlockFilter,
    Decoda\Filter\CodeFilter,
    Decoda\Filter\EmailFilter,
    Decoda\Filter\ImageFilter,
    Decoda\Filter\ListFilter,
    Decoda\Filter\QuoteFilter,
    Decoda\Filter\TextFilter,
    Decoda\Filter\UrlFilter,
    Decoda\Filter\VideoFilter;
use Decoda\Hook\CensorHook,
    Decoda\Hook\ClickableHook,
    Decoda\Hook\CodeHook,
    Decoda\Hook\EmoticonHook,
    Decoda\Hook;

/**
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2013 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class DecodaManager
{
    /**
     * Applied filters
     * @var array
     */
    protected $filters;

    /**
     * @var array
     */
    protected $hooks;

    /**
     * @var array
     */
    protected $whitelist;

    /**
     * @var Decoda
     */
    protected $value;

    /**
     * @var array
     */
    protected static $extraFilters = array();

    /**
     * @var array
     */
    protected static $extraHooks = array();

    /**
     * @var array
     */
    protected static $extraPaths = array();

    /**
     * @var string
     */
    protected $decodaPath;

    /**
     * @var string
     */
    protected $emoticonPath;

    /**
     * @var string
     */
    protected $extraEmoticonPath;

    /**
     * @param Decoda $value
     * @param array $filters
     * @param array $hooks
     * @param array $whitelist
     * @param $decodaPath
     * @param $emoticonpath
     * @param $extraEmoticonPath
     */
    public function __construct(Decoda $value,
                                array $filters = array(),
                                array $hooks = array(),
                                array $whitelist = array(),
                                $decodaPath,
                                $emoticonpath,
                                $extraEmoticonPath = ''
    )
    {
        $this->value             = $value;
        $this->filters           = $filters;
        $this->hooks             = $hooks;
        $this->whitelist         = $whitelist;
        $this->decodaPath        = $decodaPath;
        $this->emoticonPath      = $emoticonpath;
        $this->extraEmoticonPath = $extraEmoticonPath;
    }

    /**
     * @param $name
     * @param $filter
     */
    public static function addFilter($name, $filter){
        static::$extraFilters[$name] = $filter;
    }

    /**
     * @param $name
     * @param $hook
     */
    public static function addHook($name, $hook){
        static::$extraHooks[$name] = $hook;
    }

    /**
     * @param $path
     */
    public static function addTemplatePath( $path ){
        static::$extraPaths[] = $path;
    }

    /**
     * Applies filter specified in parameter
     * @param \FM\BbcodeBundle\Decoda\Decoda $code
     * @param string $filter
     *
     * @return \FM\BbcodeBundle\Decoda\Decoda
     */
    protected function applyFilter(Decoda $code, $filter)
    {
        if(isset(static::$extraFilters[$filter])){
            $extraFilter = static::$extraFilters[$filter] instanceof Filter ? static::$extraFilters[$filter] : new static::$extraFilters[$filter]();
            $code->addFilter($extraFilter);
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
    protected function applyHook(Decoda $code, $hook)
    {
        if(isset(static::$extraHooks[$hook])){
            $extraHook = static::$extraHooks[$hook] instanceof Hook ? static::$extraHooks[$hook] : new static::$extraHooks[$hook]();
            $code->addHook($extraHook);
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
                $code->addHook(new EmoticonHook(array('path' => $this->emoticonPath)));
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
    protected function applyWhitelist(Decoda $code, array $whitelist)
    {
        return $code->whitelist($whitelist);
    }

    /**
     * @return Decoda
     */
    public function getResult()
    {
        $decodaPhpEngine = new DecodaPhpEngine($this->decodaPath);

        if(!empty($this->extraEmoticonPath))
            $this->value->addPath($this->extraEmoticonPath);
        else
        $this->value->addPath($this->decodaPath.'/config/');

        foreach(static::$extraPaths as $extraPath){
            $decodaPhpEngine->setPath($extraPath);
        }

        $this->value->setEngine($decodaPhpEngine);

        foreach($this->filters as $filter)
        {
            $this->value = $this->applyFilter($this->value, $filter);
        }

        foreach($this->hooks as $hook)
        {
            $this->value = $this->applyHook($this->value, $hook);
        }

        $this->value = $this->applyWhitelist($this->value, $this->whitelist);

        return $this->value;
    }
}
