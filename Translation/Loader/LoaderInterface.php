<?php

namespace FM\BbcodeBundle\Translation\Loader;

use Symfony\Component\Config\Loader\LoaderInterface as BaseLoaderInterface;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
interface LoaderInterface extends BaseLoaderInterface
{
    /**
     * Loads a resource.
     *
     * @param mixed  $resource
     * @param string $type
     *
     * @return array An array of translation messages like:
     *               array(
     *                   'en' => array(
     *                        'id' => "translation",
     *                   ),
     *               )
     */
    public function load($resource, $type = null);
}
