<?php

namespace FM\BbcodeBundle\Tests\Decoda;

use FM\BbcodeBundle\Decoda\DecodaManager;
use Symfony\Component\HttpKernel\Config\FileLocator;

class DecodaManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DecodaManager
     */
    protected $object;

    protected function setUp()
    {
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerInterface');
        $kernel    = $this->getMock('Symfony\\Component\\HttpKernel\\KernelInterface');
        $locator   = new FileLocator($kernel);

        $options = array(
            'filter_sets' => array(
                'foo' => array(),
            ),
        );

        $this->object = new DecodaManager($container, $locator, $options);
    }

    public function testHas()
    {
        $this->assertTrue($this->object->has(DecodaManager::DECODA_DEFAULT));
        $this->assertTrue($this->object->has('foo'));
        $this->assertFalse($this->object->has('bar'));
    }

    public function testSetFilter()
    {
        $filter = $this->getMock('Decoda\\Filter');
        $this->object->setFilter('foo', $filter);
        $this->assertTrue($this->object->hasFilter('foo'));
        $this->assertSame($filter, $this->object->getFilter('foo'));
    }

    public function testSetHook()
    {
        $hook = $this->getMock('Decoda\\Hook');
        $this->object->setHook('foo', $hook);
        $this->assertTrue($this->object->hasHook('foo'));
        $this->assertSame($hook, $this->object->getHook('foo'));
    }
}
