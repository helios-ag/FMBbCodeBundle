<?php

namespace FM\BbcodeBundle\Emoticon\Matcher\Dumper;

use FM\BbcodeBundle\Emoticon\EmoticonCollection;

/**
 * MatcherDumper is the abstract class for all built-in matcher dumpers.
 *
 * @author Alexandre Quercia <alquerci@email.com>
 */
abstract class MatcherDumper implements MatcherDumperInterface
{
    /**
     * @var EmoticonCollection
     */
    private $emoticons;

    /**
     * Constructor.
     *
     * @param EmoticonCollection $emoticons The EmoticonCollection to dump
     */
    public function __construct(EmoticonCollection $emoticons)
    {
        $this->emoticons = $emoticons;
    }

    /**
     * {@inheritdoc}
     */
    public function getEmoticons()
    {
        return $this->emoticons;
    }
}
