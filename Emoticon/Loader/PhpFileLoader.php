<?php

namespace FM\BbcodeBundle\Emoticon\Loader;

use Symfony\Component\Config\Resource\FileResource;
use FM\BbcodeBundle\Emoticon\EmoticonCollection;

/**
 * PhpFileLoader loads PHP files emoticons.
 *
 * @author Alexandre Quercia <alquerci@email.com>
 */
class PhpFileLoader extends FileLoader
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
        $this->setCurrentDir(dirname($path));

        // the loader variable is exposed to the included file below
        $loader = $this;

        $collection = include $path;
        $collection->addResource(new FileResource($path));

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'php' === pathinfo($resource, PATHINFO_EXTENSION) && (!$type || 'php' === $type);
    }
}
