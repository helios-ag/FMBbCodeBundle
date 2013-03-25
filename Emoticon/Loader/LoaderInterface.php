<?php

namespace FM\BbcodeBundle\Emoticon\Loader;

use Symfony\Component\Config\Loader\LoaderInterface as BaseLoaderInterface;

use FM\BbcodeBundle\Emoticon\EmoticonCollection;

/**
 * LoaderInterface is the interface implemented by all emoticons loader classes.
 *
 * @author Alexandre Quercia <alquerci@email.com>
 */
interface LoaderInterface extends BaseLoaderInterface
{
    /**
     * Loads a resource.
     *
     * @param mixed  $resource The resource
     * @param string $type     The resource type
     *
     * @return EmoticonCollection
     */
    public function load($resource, $type = null);
}