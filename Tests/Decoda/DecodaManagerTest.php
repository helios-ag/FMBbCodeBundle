<?php

namespace FM\BbcodeBundle\Tests\Decoda;

use Decoda\Filter;
use Decoda\Hook;
use FM\BbcodeBundle\Decoda\DecodaManager;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\HttpKernel\KernelInterface;

class DecodaManagerTest extends TestCase
{
    /**
     * @var DecodaManager
     */
    protected $object;

    protected function setUp()
    {
        $container = $this->createMock(ContainerInterface::class);
        $kernel    = $this->createMock(KernelInterface::class);
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
        $filter = $this->createMock(Filter::class);
        $this->object->setFilter('foo', $filter);
        $this->assertTrue($this->object->hasFilter('foo'));
        $this->assertSame($filter, $this->object->getFilter('foo'));
    }

    public function testSetHook()
    {
        $hook = $this->createMock(Hook::class);
        $this->object->setHook('foo', $hook);
        $this->assertTrue($this->object->hasHook('foo'));
        $this->assertSame($hook, $this->object->getHook('foo'));
    }
}
