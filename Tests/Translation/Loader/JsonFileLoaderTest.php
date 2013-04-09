<?php

namespace FM\BbcodeBundle\Tests\Translation\Loader;

use FM\BbcodeBundle\Translation\Loader\JsonFileLoader;

class JsonFileLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $loader = new JsonFileLoader($this->getMock('Symfony\Component\Config\FileLocatorInterface'));
    }
}
