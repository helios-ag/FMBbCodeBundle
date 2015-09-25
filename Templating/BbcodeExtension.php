<?php

namespace FM\BbcodeBundle\Templating;

use Symfony\Component\DependencyInjection\ContainerInterface;
use FM\BbcodeBundle\Decoda\DecodaManager as DecodaManager;

/**
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2012-2015 Al Ganiev
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
     * (non-PHPdoc).
     *
     * @see Twig_Extension::getFilters()
     *
     * @return array
     */
    public function getFilters()
    {
        $options = array('is_safe' => array('html'));

        return array(
            new \Twig_SimpleFilter('bbcode_filter', array($this, 'filter'), $options),
            new \Twig_SimpleFilter('bbcode_clean', array($this, 'clean'), $options),
        );
    }

    /**
     * @param $value
     * @param $filterSet
     *
     * @return string
     * @return \FM\BbcodeBundle\Decoda\Decoda
     *
     * @throws \Twig_Error_Runtime
     */
    public function filter($value, $filterSet = DecodaManager::DECODA_DEFAULT)
    {
        if (!is_string($value)) {
            throw new \Twig_Error_Runtime('The filter can be applied to strings only.');
        }

        return $this->decodaManager->get($value, $filterSet)->parse();
    }

    /**
     * Strip tags.
     *
     * @param $value
     * @param $filterSet
     *
     * @return string
     *
     * @throws \Twig_Error_Runtime
     */
    public function clean($value, $filterSet = DecodaManager::DECODA_DEFAULT)
    {
        if (!is_string($value)) {
            throw new \Twig_Error_Runtime('The filter can be applied to strings only.');
        }

        return $this->decodaManager->get($value, $filterSet)->strip(true);
    }

    /**
     * (non-PHPdoc).
     *
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'fm_bbcode';
    }
}
