<?php

namespace FM\BbcodeBundle\Emoticon\Loader;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;
use FM\BbcodeBundle\Emoticon\Emoticon;
use FM\BbcodeBundle\Emoticon\EmoticonCollection;

/**
 * YamlFileLoader loads Yaml files emoticons.
 *
 * @author Alexandre Quercia <alquerci@email.com>
 */
class YamlFileLoader extends FileLoader
{
    /**
     * Loads the resource and return the result.
     *
     * @param mixed  $resource
     * @param string $type
     *
     * @return EmoticonCollection
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);

        $config = $this->loadFile($path);

        $collection = new EmoticonCollection();
        $collection->addResource(new FileResource($path));

        // empty file
        if (null === $config) {
            return $collection;
        }

        $this->parseImports($collection, $config, $resource);

        $this->parseEmoticons($collection, $config);

        return $collection;
    }

    /**
     * @param mixed  $resource
     * @param string $type
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'yml' === pathinfo($resource, PATHINFO_EXTENSION) && (!$type || 'yaml' === $type);
    }

    /**
     * Parse a file.
     *
     * @param string $file
     *
     * @return array
     */
    protected function loadFile($file)
    {
        return $this->validate(Yaml::parse(file_get_contents($file)), $file);
    }

    /**
     * Validates a YAML file.
     *
     * @param mixed  $content
     * @param string $file
     *
     * @return array
     *
     * @throws InvalidArgumentException When emoticon file is not valid
     */
    private function validate($content, $file)
    {
        if (null === $content) {
            return $content;
        }

        if (!is_array($content)) {
            throw new \InvalidArgumentException(sprintf('The emoticon file "%s" is not valid.', $file));
        }

        return $content;
    }

    /**
     * Parses all imports.
     *
     * @param EmoticonCollection $collection
     * @param array              $content
     * @param string             $file
     */
    private function parseImports(EmoticonCollection $collection, $content, $file)
    {
        if (!isset($content['imports'])) {
            return;
        }

        foreach ($content['imports'] as $import) {
            $this->setCurrentDir(dirname($file));
            $subCollection = $this->import($import['resource'], null, isset($import['ignore_errors']) ? (Boolean) $import['ignore_errors'] : false, $file);
            $collection->addCollection($subCollection);
        }
    }

    /**
     * Parses emoticons.
     *
     * @param EmoticonCollection $collection
     * @param array              $config
     */
    private function parseEmoticons(EmoticonCollection $collection, $config)
    {
        if (!isset($config['emoticons'])) {
            return;
        }

        foreach ($config['emoticons'] as $name => $emoticon) {
            $this->parseEmoticon($collection, $name, $emoticon);
        }
    }

    /**
     * Parses emoticons.
     *
     * @param EmoticonCollection $collection
     * @param string             $name
     * @param array              $config
     */
    private function parseEmoticon(EmoticonCollection $collection, $name, $config)
    {
        if (!isset($config['smilies']) || !is_array($config['smilies'])) {
            return;
        }

        $emoticon = new Emoticon();

        $emoticon->setSmilies($config['smilies']);

        if (isset($config['url'])) {
            $emoticon->setUrl($config['url']);
        }

        if (isset($config['html'])) {
            $emoticon->setHtml($config['html']);
        }

        if (isset($config['xHtml'])) {
            $emoticon->setXhtml($config['xHtml']);
        }

        $collection->add($name, $emoticon);
    }
}
