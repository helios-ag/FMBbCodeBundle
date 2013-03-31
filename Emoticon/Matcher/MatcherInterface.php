<?php

namespace FM\BbcodeBundle\Emoticon\Matcher;

/**
 * MatcherInterface is the interface that all emoticon matcher classes must implement.
 *
 * @author Alexandre Quercia <alquerci@email.com>
 */
interface MatcherInterface
{
    /**
     * Tries to match a smiley with a set of emoticons.
     *
     * @param string $smiley The smiley
     *
     * @return array An array of parameters
     */
    public function match($smiley);

    /**
     * Returns all smilies.
     *
     * @return string[] An array of smilies
     */
    public function getSmilies();
}
