<?php

namespace FM\BbcodeBundle\Emoticon;

use Symfony\Component\Config\Resource\ResourceInterface;

/**
 * @author Alexandre Quercia <alquerci@email.com>
 */
class EmoticonCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var Emoticon[]
     */
    private $emoticons = array();

    /**
     * @var array
     */
    private $resources = array();

    /**
     * @var string[]
     */
    private $smileyMap = array();

    public function __clone()
    {
        foreach ($this->emoticons as $name => $emoticon) {
            $this->emoticons[$name] = clone $emoticon;
        }
    }

    /**
     * Gets the number of emoticons in this collection.
     *
     * It implements \Countable.
     *
     * @return int The number of emoticons
     */
    public function count()
    {
        return count($this->emoticons);
    }

    /**
     * Gets the current EmoticonCollection as an Iterator that includes all emoticons.
     *
     * It implements \IteratorAggregate.
     *
     * @see all()
     *
     * @return \ArrayIterator An \ArrayIterator object for iterating over emoticons
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->emoticons);
    }

    /**
     * Adds an emoticon.
     *
     * @param string   $name     The emoticon name
     * @param Emoticon $emoticon A Emoticon instance
     */
    public function add($name, Emoticon $emoticon)
    {
        unset($this->emoticons[$name]);

        foreach ($emoticon as $smiley) {
            unset($this->smileyMap[$smiley]);

            $this->smileyMap[$smiley] = $emoticon;
        }

        $this->emoticons[$name] = $emoticon;
    }

    /**
     * Returns all emoticons in this collection.
     *
     * @return Emoticon[] An array of emoticons
     */
    public function all()
    {
        return $this->emoticons;
    }

    /**
     * Check weither an emoticon exists.
     *
     * @param string $name The emoticon name
     *
     * @return bool true if the emoticon exists
     */
    public function has($name)
    {
        return isset($this->emoticons[$name]);
    }

    /**
     * Gets a emoticon by name.
     *
     * @param string $name The emoticon name
     *
     * @return Emoticons|null A Emoticons instance or null when not found
     */
    public function get($name)
    {
        return isset($this->emoticons[$name]) ? $this->emoticons[$name] : null;
    }

    /**
     * Removes an emoticon or an array of emoticons by name from the collection.
     *
     * @param string|array $name The emoticon name or an array of emoticon names
     */
    public function remove($name)
    {
        foreach ((array) $name as $n) {
            foreach ($this->emoticons[$n] as $smiley) {
                unset($this->smileyMap[$smiley]);
            }
            unset($this->emoticons[$n]);
        }
    }

    /**
     * Adds an emoticon collection.
     *
     * @param EmoticonCollection $collection A EmoticonCollection instance
     */
    public function addCollection(EmoticonCollection $collection)
    {
        foreach ($collection->all() as $name => $emoticon) {
            $this->add($name, $emoticon);
        }

        $this->resources = array_merge($this->resources, $collection->getResources());
    }

    /**
     * Gets an emoticon by smiley.
     *
     * @param string $smiley
     *
     * @return null|Emoticon
     */
    public function getEmoticonBySmiley($smiley)
    {
        return isset($this->smileyMap[$smiley]) ? $this->smileyMap[$smiley] : null;
    }

    /**
     * Gets all smilies.
     *
     * @return string[]
     */
    public function getSmilies()
    {
        return array_keys($this->smileyMap);
    }

    /**
     * Returns an array of resources loaded to build this collection.
     *
     * @return ResourceInterface[] An array of resources
     */
    public function getResources()
    {
        return array_unique($this->resources);
    }

    /**
     * Adds a resource for this collection.
     *
     * @param ResourceInterface $resource A resource instance
     */
    public function addResource(ResourceInterface $resource)
    {
        $this->resources[] = $resource;
    }
}
