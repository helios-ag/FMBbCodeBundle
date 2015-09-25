<?php

namespace FM\BbcodeBundle\Tests\Decoda\Hook;

use FM\BbcodeBundle\Emoticon\Emoticon;
use FM\BbcodeBundle\Emoticon\EmoticonCollection;
use FM\BbcodeBundle\Decoda\Hook\EmoticonHook;

class EmoticonHookTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $loader = $this->getMockBuilder('FM\\BbcodeBundle\\Emoticon\\Loader\\FileLoader')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerInterface');

        $result = new EmoticonHook($loader, $container);
    }

    public function testGetMatcher()
    {
        $expectEmoticon = new Emoticon();
        $expectEmoticon->setSmiley(':foo:');

        $expectCollection = new EmoticonCollection();
        $expectCollection->add('foo', $expectEmoticon);

        $loader = $this->getMockBuilder('FM\\BbcodeBundle\\Emoticon\\Loader\\FileLoader')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
        $loader->expects($this->once())
            ->method('load')
            ->will($this->returnValue($expectCollection))
        ;

        $container = $this->getMock('Symfony\\Component\\DependencyInjection\\ContainerInterface');

        $result     = new EmoticonHook($loader, $container, array('resource' => 'bar'));
        $collection = $result->getEmoticonCollection();
        $matcher    = $result->getMatcher();

        $this->assertSame($collection->get('foo'), $expectEmoticon);
        $this->assertContains(':foo:', $matcher->getSmilies());
        $this->assertNotEmpty($matcher->match(':foo:'));
    }
}
