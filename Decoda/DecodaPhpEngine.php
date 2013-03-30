<?php

namespace FM\BbcodeBundle\Decoda;

use Decoda\Exception\IoException;
use Decoda\Engine\PhpEngine;
use Decoda\Filter;

/**
 * DecodaPhpEngine
 *
 * Renders tags by using PHP as template engine.
 *
 * @author      Miles Johnson - http://milesj.me
 * @author      Sean C. Koop - sean.koop@icans-gmbh.com
 * @copyright   Copyright 2006-2012, Miles Johnson, Inc.
 * @license     http://opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link        http://milesj.me/code/php/decoda
 */
class DecodaPhpEngine extends PhpEngine
{

    /**
     * Current path list.
     *
     * @access protected
     * @var string
     */
    protected $paths = array();

    /**
     * Current filter.
     *
     * @access protected
     * @var Filter
     */
    protected $_filter;

    public function __construct(array $config = array())
    {
        parent::__construct($config);

        $this->getPath();
    }

    /**
     * Return the current filter.
     *
     * @access public
     * @return Filter
     */
    public function getFilter()
    {
        return $this->_filter;
    }


    /**
     * Renders the tag by using php templates.
     *
     * @access public
     * @param  array $tag
     * @param  string $content
     * @return string
     * @throws IoException
     */
    public function render(array $tag, $content)
    {
        $setup = $this->getFilter()->getTag($tag['tag']);

        $paths = $this->getPaths();
        $pathMap = 0;
        foreach ($paths as $path) {
            $path = $path . $setup['template'] . '.php';
            if (file_exists($path)) {
                break;
            } else {
                $pathMap++;
            }
        }

        if ($pathMap == count((array) $paths)) {
            throw new IoException(sprintf('Template file %s does not exist.', $setup['template']));
        }

        $vars = array();

        foreach ($tag['attributes'] as $key => $value) {
            if (isset($setup['map'][$key])) {
                $key = $setup['map'][$key];
            }

            $vars[$key] = $value;
        }

        ob_start();
        extract($vars, EXTR_SKIP);
        unset($vars);
        include $path;

        return ob_get_clean();
    }

    /**
     * Sets the current filter.
     *
     * @access public
     * @param  \Decoda\Filter $filter
     * @return \Decoda\Engine|\FM\BbcodeBundle\Decoda\DecodaPhpEngine
     */
    public function setFilter(Filter $filter)
    {
        $this->_filter = $filter;

        return $this;
    }

    /**
     * Sets the path to the tag templates.
     *
     * @access public
     * @param  string $path
     * @return \Decoda\Engine|\FM\BbcodeBundle\Decoda\DecodaPhpEngine
     */
    public function setPath($path)
    {
        parent::setPath($path);

        return $this->addPath($path);
    }

    /**
     * Gets all lookup template path
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Adds the path to the templates location.
     *
     * @access public
     * @param  string $path
     * @return \Decoda\Engine|\FM\BbcodeBundle\Decoda\DecodaPhpEngine
     */
    public function addPath($path)
    {
        if (substr($path, -1) !== '/') {
            $path .= '/';
        }

        // Allow $path to overwrite all others
        array_unshift($this->paths, $path);

        return $this;
    }

}
