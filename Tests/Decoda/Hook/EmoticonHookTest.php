<?php

namespace FM\BbcodeBundle\Tests\Decoda\Hook;

use FM\BbcodeBundle\Decoda\Hook\EmoticonHook;
use FM\BbcodeBundle\Emoticon\Emoticon;
use FM\BbcodeBundle\Emoticon\EmoticonCollection;
use FM\BbcodeBundle\Emoticon\Loader\FileLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EmoticonHookTest extends TestCase
{
    public function testGetMatcher()
    {
        $expectEmoticon = new Emoticon();
        $expectEmoticon->setSmiley(':foo:');

        $expectCollection = new EmoticonCollection();
        $expectCollection->add('foo', $expectEmoticon);

        $loader = $this->getMockBuilder(FileLoader::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass()
        ;
        $loader->expects($this->once())
            ->method('load')
            ->will($this->returnValue($expectCollection))
        ;

        $container = $this->createMock(ContainerInterface::class);

        $result     = new EmoticonHook($loader, $container, array('resource' => 'bar'));
        $collection = $result->getEmoticonCollection();
        $matcher    = $result->getMatcher();

        $this->assertSame($collection->get('foo'), $expectEmoticon);
        $this->assertContains(':foo:', $matcher->getSmilies());
        $this->assertNotEmpty($matcher->match(':foo:'));
    }
}
