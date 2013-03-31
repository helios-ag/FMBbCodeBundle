<?php

namespace FM\BbcodeBundle\Emoticon\Matcher;

use FM\BbcodeBundle\Emoticon\EmoticonCollection;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
class Matcher implements MatcherInterface
{
    /**
     * @var EmoticonCollection
     */
    protected $emoticons;

    /**
     * @param EmoticonCollection $emoticons
     */
    public function __construct(EmoticonCollection $emoticons)
    {
        $this->emoticons = $emoticons;
    }

    /**
     * Tries to match a smiley with a set of emoticons.
     *
     * @param string $smiley The smiley
     *
     * @return array An array of parameters
     */
    public function match($smiley)
    {
        $emoticon = $this->emoticons->getEmoticonBySmiley($smiley);

        if (null === $emoticon) {
            return array();
        }

        return array(
            'xHtml' => $emoticon->getXhtml(),
            'html'  => $emoticon->getHtml(),
        );
    }

    /**
     * Returns all emoticons.
     *
     * @return array An array of emoticons
     */
    public function getEmoticons()
    {
        return iterator_to_array($this->emoticons, true);
    }
}