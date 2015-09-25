<?php

namespace FM\BbcodeBundle\Emoticon\Matcher\Dumper;

use FM\BbcodeBundle\Emoticon\EmoticonCollection;

/**
 * PhpMatcherDumper creates a PHP class able to match smilies for a given set of emoticons.
 *
 * @author Alexandre Quercia <alquerci@email.com>
 */
class PhpMatcherDumper extends MatcherDumper
{
    /**
     * Dumps a set of emoticons to a PHP class.
     *
     * Available options:
     *
     *  * class:      The class name
     *  * base_class: The base class name
     *
     * @param array $options An array of options
     *
     * @return string A PHP class representing the matcher class
     */
    public function dump(array $options = array())
    {
        $options = array_replace(array(
            'class'      => 'FMBbcodeBundleEmoticonMatcher',
            'base_class' => 'FM\\BbcodeBundle\\Emoticon\\Matcher\\Matcher',
        ), $options);

        return <<<EOF
<?php

/**
 * {$options['class']}
 *
 * This class has been auto-generated
 * by the Symfony FMBbcodeBundle.
 */
class {$options['class']} extends {$options['base_class']}
{
    /**
     * Constructor.
     */
    public function __construct()
    {
    }

{$this->generateMatchMethod()}

{$this->generateGetSmiliesMethod()}
}

EOF;
    }

    /**
     * Generates the code for the match method implementing SmileyMatcherInterface.
     *
     * @return string Match method as PHP code
     */
    private function generateMatchMethod()
    {
        $code = rtrim($this->compileSmileyMatch($this->getEmoticons()), "\n");

        return <<<EOF
    public function match(\$smiley)
    {
$code

        return array();
    }
EOF;
    }

    /**
     * Generates PHP code to match a EmoticonCollection with all its emoticons.
     *
     * @param EmoticonCollection $routes A EmoticonCollection instance
     *
     * @return string PHP code
     */
    private function compileSmileyMatch(EmoticonCollection $emoticons)
    {
        $code = '';
        $code .= '        switch ($smiley) {';
        foreach ($emoticons as $emoticon) {
            foreach ($emoticon as $smiley) {
                $smiley = $this->escapeForSingleQuotes($smiley);
                $xHtml  = $this->escapeForSingleQuotes($emoticon->getXhtml());
                $html   = $this->escapeForSingleQuotes($emoticon->getHtml());
                $code .= <<<EOF

            case '$smiley':
                return array(
                    'xHtml' => '$xHtml',
                    'html'  => '$html',
                );

EOF;
            }
        }
        $code .= <<<EOF
            default:
                break;
        }
EOF;

        return $code;
    }

    /**
     * Generates the code for the getEmoticons method implementing SmileyMatcherInterface.
     *
     * @return string getEmoticons method as PHP code
     */
    private function generateGetSmiliesMethod()
    {
        $code = rtrim($this->compileSmilies($this->getEmoticons()), "\n");

        return <<<EOF
    public function getSmilies()
    {
        return $code;
    }
EOF;
    }

    /**
     * Generates PHP code to match a EmoticonCollection with all its emoticons.
     *
     * @param EmoticonCollection $routes A EmoticonCollection instance
     *
     * @return string PHP code
     */
    private function compileSmilies(EmoticonCollection $emoticons)
    {
        $code = '';
        $code .= 'array(';
        foreach ($emoticons as $name => $emoticon) {
            foreach ($emoticon as $smiley) {
                $smiley = $this->escapeForSingleQuotes($smiley);

                $code .= <<<EOF

            '$smiley',
EOF;
            }
        }
        $code .= <<<EOF

        )
EOF;

        return $code;
    }

    private function escapeForSingleQuotes($value)
    {
        return str_replace(array("\x5C", "'"), array("\x5C\x5C", "\x5C'"), $value);
    }
}
