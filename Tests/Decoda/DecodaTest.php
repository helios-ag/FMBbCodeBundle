<?php

namespace FM\BbcodeBundle\Tests\Decoda;

use FM\BbcodeBundle\Decoda\Decoda;

class DecodaTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $result = new Decoda();
    }

    public function testSetLocale()
    {
        $result = new Decoda();
        $result->setDefaultLocale('en');
        $result->setLocale('azerty');

        $this->assertEquals('azerty', $result->getConfig('locale'));

        $result->setLocale('fr');

        $this->assertEquals('fr', $result->getConfig('locale'));
    }

    /**
     * @dataProvider getMessage
     */
    public function testMessage($defaultLocale, $locale, $value, $expect)
    {
        if ($expect instanceof \Exception) {
            $this->setExpectedException(get_class($expect), $expect->getMessage());
        }

        $result = new Decoda();

        $result->setMessages(array(
            'en-us' => array(
                'foo' => 'foo-en-us',
            ),
            'fr-be' => array(
                'foo' => 'foo-fr-be',
            ),
            'fr' => array(
                'foo' => 'foo-fr',
            ),
            'en' => array(
                'foo' => 'foo-en',
            ),
        ));

        if (null !== $defaultLocale) {
            $result->setDefaultLocale($defaultLocale);
        }

        if (null !== $locale) {
            $result->setLocale($locale);
        }

        $this->assertEquals($expect, $result->message($value));
    }

    public function getMessage()
    {
        return array(
            array(null, 'en', 'foo', 'foo-en'),
            array('foo', null, 'foo', new \OutOfRangeException('Localized messages for foo do not exist')),
            array(null, 'foo', 'foo', new \OutOfRangeException('Localized messages for foo do not exist')),
            array('en', null, 'foo', 'foo-en'),
            array('en', 'fr', 'foo', 'foo-fr'),
            array('en', 'be', 'foo', 'foo-fr-be'),
            array('en', 'fr-be', 'foo', 'foo-fr'),
            array('en', 'foo', 'foo', 'foo-en'),
        );
    }
}
