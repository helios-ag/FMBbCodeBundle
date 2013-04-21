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
}
