<?php

namespace FM\BbcodeBundle\Translation\Loader;

use Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;

/**
 * FileLoader is the abstract class used by all built-in loaders that are file based.
 *
 * @author Alexandre Quercia <alquerci@email.com>
 */
abstract class FileLoader extends BaseFileLoader implements LoaderInterface
{
}
