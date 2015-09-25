<?php

namespace FM\BbcodeBundle\Templating\Helper;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\Helper\Helper;
use FM\BbcodeBundle\Decoda\DecodaManager as DecodaManager;

/**
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2012 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class BbcodeHelper extends Helper
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
     * @param $value
     * @param $filterSet
     *
     * @return string
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

    public function getName()
    {
        return 'fm_bbcode';
    }
}
