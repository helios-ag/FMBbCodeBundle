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
    const DECODA_DEFAULT = "_default";

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
    private $filters = array();


    /**
     * @var Hook[]
     */
    private $hooks = array();

    /**
     * @var string
     */
    private $locale;

    /**
     * @var Decoda
     */
    private $preConfiguredDecoda;

    /**
     * @param array $options  An array of options
     */
    public function __construct(ContainerInterface $container, FileLocator $locator, FileLoader $messageLoader, array $options = array())
    {
        $this->container = $container;
        $this->locator = $locator;
        $this->messageLoader = $messageLoader;

        $this->setOptions($options);

        $this->setFilters($this->options['filters']);
        $this->setHooks($this->options['hooks']);
    }

    /**
     * Check weither a specitic filterSet exist.
     *
     * @param string $filterSet
     *
     * @return Boolean true if a specific filterSet exist
     */
    public function has($filterSet)
    {
        if (isset($this->decodaCollection[strtolower($filterSet)])
            || isset($this->options['filter_sets'][$filterSet])
            || $filterSet === self::DECODA_DEFAULT
            ) {
            return true;
        }

        return false;
    }


   /**
    * Gets a specific decoda
    *
    * @param string $string      The string to parse
    * @param string $filterSet  The specific filter_set to apply
    * @return Decoda
    * @throws \InvalidArgumentException
    */
    public function get($string, $filterSet = self::DECODA_DEFAULT)
    {
        if (!isset($this->decodaCollection[strtolower($filterSet)])) {
            // Try to create a specific filterSet throw an exception otherwise.
            if (isset($this->options['filter_sets'][$filterSet])) {
                $this->set($filterSet);
            } elseif ($filterSet === self::DECODA_DEFAULT) {
                $decoda = clone $this->getPreConfiguredDecoda();
                $decoda->defaults();
                $this->set(self::DECODA_DEFAULT, $decoda);
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'The filter_set "%s" does not exists.',
                    $filterSet
                ));
            }
        }

        $decoda = clone $this->decodaCollection[strtolower($filterSet)];

        $writeList = $decoda->getWriteList();
        $blacklist = $decoda->getBlackList();

        $decoda->reset($string);

        $decoda->whitelist($writeList);
        $decoda->blacklist($blacklist);

        return $decoda;
    }

    /**
     * Returns the filters.
     *
     * @return array The filters
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * Sets filters.
     *
     * This method implements a fluent interface.
     *
     * @param array $filters The filters
     *
     * @return DecodaManager The current DecodaManager instance
     */
    public function setFilters(array $filters)
    {
        $this->filters = array();

        return $this->addFilters($filters);
    }

    /**
     * Adds filters.
     *
     * This method implements a fluent interface.
     *
     * @param array $filters The filters
     *
     * @return DecodaManager The current DecodaManager instance
     */
    public function addFilters(array $filters)
    {
        foreach ($filters as $id => $filter) {
            if (is_string($filter)) {
                $filter = new $filter();
            }
            $this->setFilter($id, $filter);
        }

        return $this;
    }

    /**
     * Sets a filter.
     *
     * This method implements a fluent interface.
     *
     * @param string $id     A filter id
     * @param mixed  $filter The filter
     *
     * @return DecodaManager The current DecodaManager instance
     */
    public function setFilter($id, Filter $filter)
    {
        $this->filters[strtolower($id)] = $filter;

        return $this;
    }

    /**
     * Checks if a filter is set for the given id.
     *
     * @param string $id  A filter id
     *
     * @return Boolean true if the filter is set, false otherwise
     */
    public function hasFilter($id)
    {
        return isset($this->filters[strtolower($id)]);
    }

    /**
     * Gets a filter.
     *
     * @param string $id The filter id
     *
     * @return Filter The filter instance
     *
     * @throws \InvalidArgumentException
     */
    public function getFilter($id)
    {
        if (!$this->hasFilter($id)) {
            throw new \InvalidArgumentException(sprintf('You have requested a non-existent filter "%s".', $id));
        }

        return $this->filters[strtolower($id)];
    }


    /**
     * Returns the hooks.
     *
     * @return array The hooks
     */
    public function getHooks()
    {
        return $this->hooks;
    }

    /**
     * Sets hooks.
     *
     * This method implements a fluent interface.
     *
     * @param array $hooks The hooks
     *
     * @return DecodaManager The current DecodaManager instance
     */
    public function setHooks(array $hooks)
    {
        $this->hooks = array();

        return $this->addHooks($hooks);
    }

    /**
     * Adds hooks.
     *
     * This method implements a fluent interface.
     *
     * @param array $hooks The hooks
     *
     * @return DecodaManager The current DecodaManager instance
     */
    public function addHooks(array $hooks)
    {
        foreach ($hooks as $id => $hook) {
            if (is_string($hook)) {
                $hook = new $hook();
            }
            $this->setHook($id, $hook);
        }

        return $this;
    }

    /**
     * Sets a hook.
     *
     * This method implements a fluent interface.
     *
     * @param string $id    A hook id
     * @param mixed  $hook  The hook
     *
     * @return DecodaManager The current DecodaManager instance
     */
    public function setHook($id, Hook $hook)
    {
        $this->hooks[strtolower($id)] = $hook;

        return $this;
    }

    /**
     * Checks if a hook is set for the given id.
     *
     * @param string $id  A hook id
     *
     * @return Boolean true if the hook is set, false otherwise
     */
    public function hasHook($id)
    {
        return isset($this->hooks[strtolower($id)]);
    }

    /**
     * Gets a hook.
     *
     * @param string $id The hook id
     *
     * @return Hook The hook instance
     *
     * @throws \InvalidArgumentException
     */
    public function getHook($id)
    {
        if (!$this->hasHook($id)) {
            throw new \InvalidArgumentException(sprintf('You have requested a non-existent hook "%s".', $id));
        }

        return $this->hooks[strtolower($id)];
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
    private function setOptions(array $options)
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
    private function setOption($key, $value)
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
    private function getOption($key)
    {
        if (!array_key_exists($key, $this->options)) {
            throw new \InvalidArgumentException(sprintf('The DecodaManager does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }

    /**
     * Applies filter specified in parameter
     * @param \FM\BbcodeBundle\Decoda\Decoda $code
     * @param string                         $id
     *
     * @return \FM\BbcodeBundle\Decoda\Decoda
     */
    private function applyFilter(Decoda $code, $id)
    {
        $id = strtolower($id);

        if ($this->hasFilter($id)) {
            return $code->addFilter($this->getFilter($id), $id);
        }

        switch ($id) {
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
     * @param $id
     * @return Decoda
     */
    private function applyHook(Decoda $code, $id)
    {
        $id = strtolower($id);

        if ($this->hasHook($id)) {
            return $code->addHook($this->getHook($id), $id);
        }

        switch ($id) {
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
    private function applyWhitelist(Decoda $code, array $whitelist)
    {
        return $code->whitelist($whitelist);
    }


    /**
     * Gets a pre-configured decoda.
     *
     * @return Decoda
     */
    private function getPreConfiguredDecoda()
    {
        if (null !== $this->preConfiguredDecoda) {
            return $this->preConfiguredDecoda;
        }

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

        $this->preConfiguredDecoda = $decoda;

        return $decoda;
    }


    /**
     * @param string $filterName
     * @param Decoda $decoda
     */
    private function set($filterSet, Decoda $decoda = null)
    {
        if (null !== $decoda) {
            $this->decodaCollection[strtolower($filterSet)] = $decoda;
            return;
        }

        $decoda = clone $this->getPreConfiguredDecoda();
        if (isset($this->options['filter_sets'][$filterSet])) {
            $options = $this->options['filter_sets'][$filterSet];
        } else {
            $this->decodaCollection[strtolower($filterSet)] = $decoda;
            return;
        }

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

        $this->decodaCollection[strtolower($filterSet)] = $decoda;
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
                // TODO use bundle hierachy (the third parameter of locate)
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
