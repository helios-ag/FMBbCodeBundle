<?php

namespace FM\BbcodeBundle\Decoda;
use Decoda\Engine\PhpEngine;
use Decoda\Filter;
use Exception;
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
     * Current path.
     *
     * @access protected
     * @var string
     */
    protected $_path = array();

    /**
     * Current filter.
     *
     * @access protected
     * @var Filter
     */
    protected $_filter;

    public function __construct()
    {
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
     * @throws \Exception
     * @return string
     * @throws Exception
     */
    public function render(array $tag, $content)
    {
        $setup = $this->getFilter()->tag($tag['tag']);

        $paths = $this->getPath();
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
            throw new Exception(sprintf('Template file %s does not exist.', $setup['template']));
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
        if (substr($path, -1) !== '/') {
            $path .= '/';
        }

        // Allow $path to overwrite all others
        array_unshift($this->_path, $path);

        return $this;
    }

}
