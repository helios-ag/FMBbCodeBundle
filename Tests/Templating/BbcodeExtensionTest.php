<?php

namespace FM\BbcodeBundle\Tests\Templating;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\Yaml\Parser;
use Symfony\Component\DependencyInjection\Reference;
use FM\BbcodeBundle\Tests\TwigBasedTestCase;

class BbcodeExtensionTest extends TwigBasedTestCase
{
    /**
     * @dataProvider dataDefaultTags
     */
    public function testDefaultFilter($value, $expected) {
        $this->assertSame($expected,
            $this->getTwig()->render('FunctionalTestBundle:filters:default.html.twig', array(
                'value' => $value,
            )));
    }

    public function dataDefaultTags() {
        return array(
            array('[b]bold[/b]', "<strong>bold</strong>"),
            array('[i]italic[/i]', "<em>italic</em>"),
            array('[u]underline[/u]', "<u>underline</u>"),
            array('[s]strikeout[/s]', "<del>strikeout</del>"),
            array('[sub]subscript[/sub]', "<sub>subscript</sub>"),
            array('[sup]superscript[/sup]', "<sup>superscript</sup>"),
            array('[abbr="Object relational mapper"]ORM[/abbr]', '<abbr title="Object relational mapper">ORM</abbr>'),

        );
    }

    /**
     * @dataProvider dataUrlTags
     */
    public function testUrlFilter($value, $expected) {
        $this->assertSame($expected,
            $this->getTwig()->render('FunctionalTestBundle:filters:url.html.twig', array(
                'value' => $value,
            )));
    }

    public function dataUrlTags() {
        return array(
            array('[url]http://example.org[/url]','<a href="http://example.org">http://example.org</a>'),
            array('[url="http://example.com"]Example[/url]','<a href="http://example.com">Example</a>')
        );
    }

    /**
     * @dataProvider dataImgTags
     */
    public function testImgFilter($value, $expected) {
        $this->assertSame($expected,
            $this->getTwig()->render('FunctionalTestBundle:filters:image.html.twig', array(
                'value' => $value,
            )));
    }

    public function dataImgTags() {
        return array(
            array('[img]http://github.com/picture.jpg[/img]','<img src="http://github.com/picture.jpg" alt="" />'),
            array('[img width="500"]http://github.com/picture.jpg[/img]','<img width="500" src="http://github.com/picture.jpg" alt="" />')
        );
    }
}