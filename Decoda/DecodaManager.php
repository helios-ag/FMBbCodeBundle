<?php
namespace FM\BbcodeBundle\Decoda;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Symfony\Component\HttpKernel\Config\FileLocator;
use FM\BbcodeBundle\Translation\Loader\FileLoader;

use FM\BbcodeBundle\Decoda\Decoda;
use FM\BbcodeBundle\Decoda\DecodaPhpEngine;
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
     * @var FileLocator
     */
    protected $locator;

    /**
     * @var FileLoader
     */
    protected $messageLoader;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Decoda[]
     */
    private $decodaCollection;

    /**
     * @var array
     */
    private $options = array();


    /**
     * @var DecodaPhpEngine
     */
    private $phpEngine;


    /**
     * @var Filter[]
     */
    private $filters;


    /**
     * @var Hook[]
     */
    private $hooks;

    /**
     * @var string
     */
    private $locale;

    /**
     * @param array $options  An array of options
     */
    public function __construct(ContainerInterface $container, FileLocator $locator, FileLoader $messageLoader, array $options = array())
    {
        $this->container = $container;
        $this->locator = $locator;
        $this->messageLoader = $messageLoader;

        $this->setOptions($options);
        $this->compileFilters();
        $this->compileHooks();
        $this->compileDecoda();
    }

   /**
    * Gets a specific decoda
    *
    * @param string $string      The string to parse
    * @param string $filterSet  The specific filter_set to apply
    * @return Decoda
    * @throws \InvalidArgumentException
    */
    public function getDecoda($string, $filterSet = '_default')
    {
        if (!array_key_exists($filterSet, $this->decodaCollection)) {
            throw new \InvalidArgumentException(sprintf('The filter_set "%s" does not exists.', $filterName));
        }

        $decoda = $this->decodaCollection[$filterSet];

        $writeList = $decoda->getWriteList();
        $blacklist = $decoda->getBlackList();

        $decoda->reset($string);

        $decoda->whitelist($writeList);
        $decoda->blacklist($blacklist);

        return $decoda;
    }

    /**
     * Sets options.
     *
     * Available options:
     *
     *   * filters:
     *   * hooks:
     *   * messages:
     *   * templates:
     *   * emoticonpath:
     *   * extraemoticonpath:
     *   * filter_sets:
     *   * default_locale:
     *   * resources:
     *
     * @param array $options An array of options
     *
     * @throws \InvalidArgumentException When unsupported option is provided
     */
    public function setOptions(array $options)
    {
        $this->options = array(
            'filters'            => array(),
            'hooks'              => array(),
            'messages'           => null,
            'templates'          => array(),
            'emoticonpath'       => '/emoticons/',
            'extraemoticonpath'  => null,
            'filter_sets'        => array(),
            'resources'          => array(),
            'default_locale'     => 'en',
        );

        // check option names and live merge, if errors are encountered Exception will be thrown
        $invalid = array();
        foreach ($options as $key => $value) {
            if (array_key_exists($key, $this->options)) {
                $this->options[$key] = $value;
            } else {
                $invalid[] = $key;
            }
        }

        if ($invalid) {
            throw new \InvalidArgumentException(sprintf('The DecodaManager does not support the following options: "%s".', implode('\', \'', $invalid)));
        }
    }

    /**
     * Sets an option.
     *
     * @param string $key   The key
     * @param mixed  $value The value
     *
     * @throws \InvalidArgumentException
     */
    public function setOption($key, $value)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The DecodaManager does not support the "%s" option.', $key));
        }

        $this->options[$key] = $value;
    }

    /**
     * Gets an option value.
     *
     * @param string $key The key
     *
     * @return mixed The value
     *
     * @throws \InvalidArgumentException
     */
    public function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The DecodaManager does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }

    /**
     * Applies filter specified in parameter
     * @param \FM\BbcodeBundle\Decoda\Decoda $code
     * @param string                         $filter
     *
     * @return \FM\BbcodeBundle\Decoda\Decoda
     */
    protected function applyFilter(Decoda $code, $filter)
    {
        if (isset($this->filters[$filter])) {
            $code->addFilter($this->filters[$filter]);

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
        if (isset($this->hooks[$hook])) {
            $code->addHook($this->hooks[$hook]);

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
                $code->addHook(new EmoticonHook(array('path' => $this->options['emoticonpath'])));
                break;
            case 'code':
                $code->addHook(new CodeHook());
                break;
        }

        return $code;
    }

    /**
     * @param  Decoda $code
     * @param  array  $whitelist
     * @return Decoda
     */
    protected function applyWhitelist(Decoda $code, array $whitelist)
    {
        return $code->whitelist($whitelist);
    }


    /**
     * Compile decoda for all filter_sets.
     */
    private function compileDecoda()
    {
        $decoda = new Decoda();

        if (null !== $this->options['messages']) {
            $decoda->addMessages($this->messageLoader->load($this->options['messages']));
        }

        if (!empty($this->options['extraemoticonpath'])) {
            $decoda->addPath($this->locator->locate($this->options['extraemoticonpath']));
        }

        foreach ($this->options['resources'] as $resource) {
            $decoda->addPath($this->locator->locate($resource));
        }

        $decoda->setEngine($this->getPhpEngine());

        $decoda->setDefaultLocale($this->options['default_locale']);
        $decoda->setLocale($this->getLocale());

        $newDecoda = clone $decoda;
        $newDecoda->defaults();
        $this->decodaCollection['_default'] = $newDecoda;

        foreach ($this->options['filter_sets'] as $filterSet => $options) {
            $newDecoda = clone $decoda;
            $this->addDecoda($filterSet, $options, $newDecoda);
        }
    }

    private function compileFilters()
    {
        foreach ($this->options['filters'] as $filter) {
            $this->filters[$filter['classname']] = new $filter['class']();
        }
    }

    private function compileHooks()
    {
        foreach ($this->options['hooks'] as $hook) {
            $this->hooks[$hook['classname']] = new $hook['class']();
        }
    }

    /**
     * @param string $filterName
     * @param array $options
     * @param Decoda $decoda
     */
    private function addDecoda($filterSet, array $options, Decoda $decoda)
    {
        if (!empty($options['locale']) && 'default' != $options['locale']) {
            $decoda->setLocale($options['locale']);
        }

        $decoda->setXhtml($options['xhtml']);
        $decoda->setStrict($options['strict']);


        foreach ($options['filters'] as $filter) {
            $this->applyFilter($decoda, $filter);
        }

        foreach ($options['hooks'] as $hook) {
            $this->applyHook($decoda, $hook);
        }

        $this->applyWhitelist($decoda, $options['whitelist']);

        $this->decodaCollection[$filterSet] = $decoda;
    }


    /**
     * Gets a DecodaPhpEngine
     *
     * @return DecodaPhpEngine
     */
    private function getPhpEngine()
    {
        if (null === $this->phpEngine) {
            $this->phpEngine = new DecodaPhpEngine();

            foreach ($this->options['templates'] as $template) {
                $path = $this->locator->locate($template['path']);
                $this->phpEngine->addPath($path);
            }
        }

        return $this->phpEngine;
    }

    private function getLocale()
    {
        if (null === $this->locale) {
            if ($this->container->isScopeActive('request') && $this->container->has('request')) {
                $this->locale = $this->container->get('request')->getLocale();
            } else {
                $this->locale = $this->options['default_locale'];
            }
        }

        return $this->locale;
    }
}
