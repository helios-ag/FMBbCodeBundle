<?php

namespace FM\BbcodeBundle\Translation\Loader;

use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;

/**
 * FileLoader is the abstract class used by all built-in loaders that are file based.
 *
 * @author Alexandre Quercia <alquerci@email.com>
 */
abstract class FileLoader extends BaseFileLoader
{
    /**
     * Constructor.
     *
     * @param FileLocator $locator A FileLocator instance
     */
    public function __construct(FileLocator $locator)
    {
        parent::__construct($locator);
    }

    /**
     * Loads a resource.
     *
     * @see \Symfony\Component\Config\Loader\LoaderInterface::load()
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
    abstract public function load($resource, $type = null);
}
