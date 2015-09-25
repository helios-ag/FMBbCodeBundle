<?php

namespace FM\BbcodeBundle\Tests\Emoticon;

use FM\BbcodeBundle\Emoticon\Emoticon;
use FM\BbcodeBundle\Emoticon\EmoticonCollection;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
class EmoticonCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testAddOverriddenEmoticon()
    {
        $collection = new EmoticonCollection();
        $emoticon   = new Emoticon();
        $emoticon->setSmilies(array(':foo:'));
        $emoticon1 = new Emoticon();
        $emoticon1->setSmilies(array(':bar:'));
        $emoticon2 = new Emoticon();
        $emoticon2->setSmilies(array(':foofoo:'));

        $collection->add('foo', $emoticon);
        $collection->add('bar', $emoticon1);
        $collection->add('foo', $emoticon2);

        $this->assertSame(array(':foo:', ':bar:', ':foofoo:'), $collection->getSmilies());
        $this->assertSame(array('bar' => $emoticon1, 'foo' => $emoticon2), $collection->all());
        $this->assertSame($emoticon1, $collection->getEmoticonBySmiley(':bar:'));
        $this->assertSame($emoticon, $collection->getEmoticonBySmiley(':foo:'));
        $this->assertSame($emoticon2, $collection->getEmoticonBySmiley(':foofoo:'));
    }

    public function testAddOverriddenSmiley()
    {
        $collection = new EmoticonCollection();
        $emoticon   = new Emoticon();
        $emoticon->setSmilies(array(':foo:', ':foofoo:'));
        $emoticon1 = new Emoticon();
        $emoticon1->setSmilies(array(':foo:', ':bar:'));

        $collection->add('foo', $emoticon);
        $collection->add('bar', $emoticon1);

        $this->assertSame(array(':foofoo:', ':foo:', ':bar:'), $collection->getSmilies());
        $this->assertSame($emoticon, $collection->getEmoticonBySmiley(':foofoo:'));
        $this->assertSame($emoticon1, $collection->getEmoticonBySmiley(':foo:'));
        $this->assertSame($emoticon1, $collection->getEmoticonBySmiley(':bar:'));
    }
}
