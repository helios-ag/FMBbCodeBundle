<?php

namespace FM\BbcodeBundle\Emoticon;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
class Emoticon implements \IteratorAggregate
{
    /**
     * @var string[]
     */
    private $smilies = array();

    /**
     * @var string
     */
    private $htmlRepr;

    /**
     * @var string
     */
    private $xhtmlRepr;

    /**
     * @var string
     */
    private $url;

    /**
     * Gets the emoticon url.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Sets the emoticon url.
     *
     * @param string $url
     *
     * @return Emoticon
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return  $this;
    }

    /**
     * Gets the html representation.
     *
     * @return string
     */
    public function getHtml()
    {
        if (null !== $this->htmlRepr) {
            return $this->htmlRepr;
        }

        if (null !== $this->getUrl()) {
            $this->htmlRepr = sprintf('<img src="%s" alt="" >', $this->getUrl());
        }

        return $this->htmlRepr;
    }

    /**
     * Sets the html representation.
     *
     * @param string $html
     *
     * @return Emoticon
     */
    public function setHtml($html)
    {
        $this->htmlRepr = $html;

        return  $this;
    }

    /**
     * Gets the xhtml representation.
     *
     * @return string
     */
    public function getXhtml()
    {
        if (null !== $this->xhtmlRepr) {
            return $this->xhtmlRepr;
        }

        if (null !== $this->getUrl()) {
            $this->xhtmlRepr = sprintf('<img src="%s" alt="" />', $this->getUrl());
        }

        return $this->xhtmlRepr;
    }

    /**
     * Sets the xhtml representation.
     *
     * @param string $xhtml
     *
     * @return Emoticon
     */
    public function setXhtml($xhtml)
    {
        $this->xhtmlRepr = $xhtml;

        return  $this;
    }

    /**
     * Returns the smilies.
     *
     * @return string[] The smilies
     */
    public function getSmilies()
    {
        return array_keys($this->smilies);
    }

    /**
     * Sets smilies.
     *
     * This method implements a fluent interface.
     *
     * @param string[] $smilies The smilies
     *
     * @return Emoticon The current Emoticon instance
     */
    public function setSmilies(array $smilies)
    {
        $this->smilies = array();

        return $this->addSmilies($smilies);
    }

    /**
     * Adds smilies.
     *
     * This method implements a fluent interface.
     *
     * @param string[] $smilies The smilies
     *
     * @return Emoticon The current Emoticon instance
     */
    public function addSmilies(array $smilies)
    {
        foreach ($smilies as $smiley) {
            $this->setSmiley($smiley);
        }

        return $this;
    }

    /**
     * Sets a smiley.
     *
     * This method implements a fluent interface.
     *
     * @param string $smiley The smiley
     *
     * @return Emoticon The current Emoticon instance
     */
    public function setSmiley($smiley)
    {
        $this->smilies[$smiley] = true;

        return $this;
    }

    /**
     * Checks if a smile is set for the given id.
     *
     * @param string $smile A smile
     *
     * @return bool true if the smile is set, false otherwise
     */
    public function hasSmiley($smiley)
    {
        return isset($this->smilies[$smiley]);
    }

    /**
     * Gets the current Emoticon as an Iterator that includes all smilies.
     *
     * It implements \IteratorAggregate.
     *
     * @return \ArrayIterator An \ArrayIterator object for iterating over smilies
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getSmilies());
    }
}
