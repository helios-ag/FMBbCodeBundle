<?php

namespace FM\BbcodeBundle\Decoda\Hook;

use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\LoaderInterface;
use Decoda\Decoda;
use Decoda\Hook\EmoticonHook as BaseEmoticonHook;
use FM\BbcodeBundle\Emoticon\Emoticon;
use FM\BbcodeBundle\Emoticon\EmoticonCollection;
use FM\BbcodeBundle\Emoticon\Matcher\MatcherInterface;

/**
 * Converts smiley faces into emoticon images.
 *
 * @author Alexandre Quercia <alquerci@email.com>
 */
class EmoticonHook extends BaseEmoticonHook implements CacheWarmerInterface
{
    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var EmoticonCollection|null
     */
    protected $collection;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var MatcherInterface
     */
    protected $matcher;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Constructor.
     *
     * @param LoaderInterface $loader   A LoaderInterface instance
     * @param mixed           $resource The main resource to load
     * @param array           $options  An array of options
     */
    public function __construct(LoaderInterface $loader, ContainerInterface $container, array $options = array())
    {
        $this->loader    = $loader;
        $this->container = $container;
        $this->setOptions($options);

        parent::__construct($options);
    }

    /**
     * Sets options.
     *
     * Available options:
     *
     *   * path: The web path for emoticons images (optional)
     *   * extension: The extension for emoticons images (optional)
     *   * resource: The main resource (optional)
     *   * resource_type: Type hint for the main resource (optional)
     *
     * @param array $options An array of options
     *
     * @throws \InvalidArgumentException When unsupported option is provided
     */
    public function setOptions(array $options)
    {
        $this->options = array(
            'path'                   => '/images/',
            'extension'              => 'png',
            'resource'               => null,
            'resource_type'          => null,
            'cache_dir'              => null,
            'matcher_class'          => 'FM\\BbcodeBundle\\Emoticon\\Matcher\\Matcher',
            'matcher_base_class'     => 'FM\\BbcodeBundle\\Emoticon\\Matcher\\Matcher',
            'matcher_dumper_class'   => 'FM\\BbcodeBundle\\Emoticon\\Matcher\\Dumper\\PhpMatcherDumper',
            'matcher_cache_class'    => 'FMBbcodeBundleEmoticonMatcher',
            'debug'                  => false,
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
            throw new \InvalidArgumentException(sprintf('The EmoticonHook does not support the following options: "%s".', implode('\', \'', $invalid)));
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
            throw new \InvalidArgumentException(sprintf('The EmoticonHook does not support the "%s" option.', $key));
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
            throw new \InvalidArgumentException(sprintf('The EmoticonHook does not support the "%s" option.', $key));
        }

        return $this->options[$key];
    }

    /**
     * Gets the EmoticonCollection instance associated with this EmoticonHook.
     *
     * @return EmoticonCollection A EmoticonCollection instance
     */
    public function getEmoticonCollection()
    {
        if (null !== $this->collection) {
            return $this->collection;
        }

        $this->collection = new EmoticonCollection();

        // Convert a default decoda emoticons array to an EmoticonCollection
        $collection = new EmoticonCollection();

        if (!$this->getEmoticons()) {
            if (null === $this->getParser()) {
                $this->setParser(new Decoda());
            }

            $this->startup();
        }

        foreach ($this->getEmoticons() as $name => $smilies) {
            $emoticon = new Emoticon();
            foreach ($smilies as $smiley) {
                $emoticon->setSmiley($smiley);
            }
            $collection->add($name, $emoticon);
        }

        $this->collection->addCollection($collection);

        if (null !== $this->options['resource']) {
            $subCollection = $this->loader->load($this->options['resource'], $this->options['resource_type']);
            $this->collection->addCollection($subCollection);
        }

        $this->resolveParameters($this->collection);

        return $this->collection;
    }

    /**
     * Gets the Matcher instance associated with this EmoticonHook.
     *
     * @return MatcherInterface A MatcherInterface instance
     */
    public function getMatcher()
    {
        if (null !== $this->matcher) {
            return $this->matcher;
        }

        if (null === $this->options['cache_dir'] || null === $this->options['matcher_cache_class']) {
            return $this->matcher = new $this->options['matcher_class']($this->getEmoticonCollection());
        }

        $class     = $this->options['matcher_cache_class'];
        $cacheFile = $this->options['cache_dir'].'/'.$class.'.php';
        $cache     = new ConfigCache($cacheFile, $this->options['debug']);
        if (!$cache->isFresh($class)) {
            $dumper = new $this->options['matcher_dumper_class']($this->getEmoticonCollection());

            $options = array(
                'class'      => $class,
                'base_class' => $this->options['matcher_base_class'],
            );

            $cache->write($dumper->dump($options), $this->getEmoticonCollection()->getResources());
        }

        require_once $cacheFile;

        return $this->matcher = new $class();
    }

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $currentDir = $this->getOption('cache_dir');

        // force cache generation
        $this->setOption('cache_dir', $cacheDir);
        $this->getMatcher();

        $this->setOption('cache_dir', $currentDir);
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return false;
    }

    /**
     * Returns all available smilies.
     *
     * @return string[] An array of smiley
     */
    public function getSmilies()
    {
        return $this->getMatcher()->getSmilies();
    }

    /**
     * Checks if a smiley is set for the given id.
     *
     * @param string $smiley A smiley
     *
     * @return bool true if the smiley is set, false otherwise
     */
    public function hasSmiley($smiley)
    {
        return (boolean) $this->getMatcher()->match($smiley);
    }

    /**
     * Convert a smiley to html representation.
     *
     * @param string $smiley  A smiley
     * @param bool   $isXhtml Ask for respected the xHtml standard code
     *
     * @return string
     */
    public function render($smiley, $isXhtml = true)
    {
        $parameters = $this->getMatcher()->match($smiley);

        if (empty($parameters)) {
            return $smiley;
        }

        return $isXhtml ? $parameters['xHtml'] : $parameters['html'];
    }

    /**
     * Replaces placeholders with service container parameter values in:
     * - the emoticon url,
     * - the emoticon html,
     * - the emoticon xHtml.
     *
     * @param EmoticonCollection $collection
     */
    private function resolveParameters(EmoticonCollection $collection)
    {
        foreach ($collection as $name => $emoticon) {
            $emoticon->setUrl($this->resolve($emoticon->getUrl()));

            if (!$emoticon->getUrl()) {
                // Sets emoticon url
                $emoticon->setUrl(sprintf('%s%s.%s',
                    $this->options['path'],
                    $name,
                    $this->options['extension']
                ));
            }

            $emoticon->setHtml($this->resolve($emoticon->getHtml()));
            $emoticon->setXhtml($this->resolve($emoticon->getXhtml()));
        }
    }

    /**
     * Recursively replaces placeholders with the service container parameters.
     *
     * @param mixed $value The source which might contain "%placeholders%"
     *
     * @return mixed The source with the placeholders replaced by the container
     *               parameters. Array are resolved recursively.
     *
     * @throws ParameterNotFoundException When a placeholder does not exist as a container parameter
     * @throws RuntimeException           When a container value is not a string or a numeric value
     */
    private function resolve($value)
    {
        if (is_array($value)) {
            foreach ($value as $key => $val) {
                $value[$key] = $this->resolve($val);
            }

            return $value;
        }

        if (!is_string($value)) {
            return $value;
        }

        $container = $this->container;

        $escapedValue = preg_replace_callback('/%%|%([^%\s]+)%/', function ($match) use ($container, $value) {
            // skip %%
            if (!isset($match[1])) {
                return '%%';
            }

            $key = strtolower($match[1]);

            if (!$container->hasParameter($key)) {
                throw new ParameterNotFoundException($key);
            }

            $resolved = $container->getParameter($key);

            if (is_string($resolved) || is_numeric($resolved)) {
                return (string) $resolved;
            }

            throw new RuntimeException(sprintf(
                'A string value must be composed of strings and/or numbers,'.
                'but found parameter "%s" of type %s inside string value "%s".',
                $key,
                gettype($resolved),
                $value)
            );

        }, $value);

        return str_replace('%%', '%', $escapedValue);
    }
}
