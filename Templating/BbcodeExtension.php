<?php

namespace FM\BbcodeBundle\Templating;

use FM\BbcodeBundle\Translation\Loader\FileLoader;

use Symfony\Component\DependencyInjection\ContainerInterface;
use FM\BbcodeBundle\Decoda\Decoda as Decoda;
use FM\BbcodeBundle\Decoda\DecodaManager as DecodaManager;

/**
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2012 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class BbcodeExtension extends \Twig_Extension
{
    /**
     * @var DecodaManager
     */
    protected $decodaManager;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(DecodaManager $decodaManager)
    {
        $this->decodaManager = $decodaManager;
    }

    /**
     * (non-PHPdoc)
     * @see Twig_Extension::getFilters()
     * @return array
     */
    public function getFilters()
    {
        return array(
            'bbcode_filter' => new \Twig_Filter_Method($this, 'filter', array('is_safe' => array('html'))),
            'bbcode_clean' => new \Twig_Filter_Method($this, 'clean', array('is_safe' => array('html')))
        );
    }

    /**
     * @param $value
     * @param $filter
     * @return string
     * @throws \Twig_Error_Runtime
     * @return \FM\BbcodeBundle\Decoda\Decoda
     */
    public function filter($value, $filter)
    {
        if (!is_string($value)) {
            throw new \Twig_Error_Runtime('The filter can be applied to strings only.');
        }

        return $this->decodaManager->getDecoda($value, $filter)->parse();
    }

    /**
     *
     * Strip tags
     * @param $value
     * @return string
     * @throws \Twig_Error_Runtime
     */
    public function clean($value)
    {
        if (!is_string($value)) {
            throw new \Twig_Error_Runtime('The filter can be applied to strings only.');
        }

        return $this->decodaManager->getDecoda($value)->strip(true);
    }

    /**
     * (non-PHPdoc)
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'fm_bbcode';
    }
}
