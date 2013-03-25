<?php

namespace FM\BbcodeBundle\Emoticon\Matcher\Dumper;

use FM\BbcodeBundle\Emoticon\EmoticonCollection;

/**
 * MatcherDumperInterface is the interface that all matcher dumper classes must implement.
 *
 * @author Alexandre Quercia <alquerci@email.com>
 */
interface MatcherDumperInterface
{
    /**
     * Dumps a set of emoticons to a string representation of executable code
     * that can then be used to match a smiley against these emoticons.
     *
     * @param array $options An array of options
     *
     * @return string Executable code
     */
    public function dump(array $options = array());

    /**
     * Gets the routes to dump.
     *
     * @return EmoticonCollection A EmoticonCollection instance
     */
    public function getEmoticons();
}
