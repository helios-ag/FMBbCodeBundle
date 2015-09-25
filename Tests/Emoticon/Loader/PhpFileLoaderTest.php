<?php

namespace FM\BbcodeBundle\Tests\Emoticon\Loader;

use Symfony\Component\Config\FileLocator;
use FM\BbcodeBundle\Emoticon\Loader\PhpFileLoader;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
class PhpFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers FM\BbcodeBundle\Emoticon\Loader\PhpFileLoader::supports
     */
    public function testSupports()
    {
        $loader = new PhpFileLoader(new FileLocator());

        $this->assertTrue($loader->supports('foo.php'), '->supports() returns true if the resource is loadable');
        $this->assertFalse($loader->supports('foo.foo'), '->supports() returns true if the resource is loadable');
    }

    /**
     * @covers FM\BbcodeBundle\Emoticon\Loader\PhpFileLoader::load
     */
    public function testLoad()
    {
        $loader = new PhpFileLoader(new FileLocator());

        $collection = $loader->load(__DIR__.'/../../Fixtures/Emoticon/php/simple.php');

        $this->assertInstanceOf('FM\BbcodeBundle\Emoticon\EmoticonCollection', $collection);
        $this->assertTrue($collection->has('foo'), '->load() loads a PHP file resource');
        $this->assertCount(1, $collection->getResources());
    }

    /**
     * @covers FM\BbcodeBundle\Emoticon\Loader\PhpFileLoader::load
     */
    public function testLoadSupportImport()
    {
        $loader = new PhpFileLoader(new FileLocator());

        $collection = $loader->load(__DIR__.'/../../Fixtures/Emoticon/php/import.php');
        $this->assertCount(2, $collection->getResources());
    }
}
