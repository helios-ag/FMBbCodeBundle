<?php

namespace FM\BbcodeBundle\Decoda;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Decoda\Engine\PhpEngine;
use Decoda\Loader\FileLoader;
use Decoda\Filter;
use Decoda\Hook;

/**
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2013 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class DecodaManager
{
    const DECODA_DEFAULT = '_default';

    /**
     * @var FileLocator
     */
    protected $locator;

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
     * @var PhpEngine
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
     * @param array $options An array of options
     */
    public function __construct(ContainerInterface $container, FileLocator $locator, array $options = array())
    {
        $this->container = $container;
        $this->locator   = $locator;

        $this->setOptions($options);
    }

    /**
     * Check weither a specitic filterSet exist.
     *
     * @param string $filterSet
     *
     * @return bool true if a specific filterSet exist
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
     * Gets a specific decoda.
     *
     * @param string $string    The string to parse
     * @param string $filterSet The specific filter_set to apply
     *
     * @return Decoda
     *
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

        $writelist = $decoda->getWhitelist();
        $blacklist = $decoda->getBlacklist();

        $decoda->reset($string);

        $decoda->whitelist($writelist);
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
     * @param string $id A filter id
     *
     * @return bool true if the filter is set, false otherwise
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
            $this->setHook($id, $hook);
        }

        return $this;
    }

    /**
     * Sets a hook.
     *
     * This method implements a fluent interface.
     *
     * @param string $id   A hook id
     * @param mixed  $hook The hook
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
     * @param string $id A hook id
     *
     * @return bool true if the hook is set, false otherwise
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
     *   * messages:
     *   * templates:
     *   * filter_sets:
     *   * default_locale:
     *
     * @param array $options An array of options
     *
     * @throws \InvalidArgumentException When unsupported option is provided
     */
    private function setOptions(array $options)
    {
        $this->options = array(
            'messages'           => null,
            'templates'          => array(),
            'filter_sets'        => array(),
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
            $decoda->addMessages(new FileLoader($this->locator->locate($this->options['messages'])));
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
            return;
        }

        if (!empty($options['locale']) && 'default' != $options['locale']) {
            $decoda->setLocale($options['locale']);
        }

        $decoda->setXhtml($options['xhtml']);
        $decoda->setStrict($options['strict']);
        $decoda->setEscaping($options['escaping']);
        $decoda->setConfig(array('lineBreaks' => $options['line_breaks']));

        foreach ($options['filters'] as $id) {
            $decoda->addFilter($this->getFilter($id), $id);
        }

        foreach ($options['hooks'] as $id) {
            $decoda->addHook($this->getHook($id), $id);
        }

        $decoda->whitelist($options['whitelist']);

        $this->decodaCollection[strtolower($filterSet)] = $decoda;
    }

    /**
     * Gets a DecodaPhpEngine.
     *
     * @return DecodaPhpEngine
     */
    private function getPhpEngine()
    {
        if (null === $this->phpEngine) {
            $this->phpEngine = new PhpEngine();

            foreach ($this->options['templates'] as $template) {
                // Use bundle hierachy
                $paths = $this->locator->locate($template['path'], null, false);

                foreach ($paths as $path) {
                    $this->phpEngine->addPath($path);
                }
            }

            $decoda       = new Decoda();
            $defaultPaths = $decoda->getEngine()->getPaths();
            foreach ($defaultPaths as $path) {
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
