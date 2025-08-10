<?php

namespace FM\BbcodeBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Twig\Error\RuntimeError;
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
     * @param mixed $value
     * @param string $filterSet
     *
     * @return string
     *
     * @throws RuntimeError
     */
    public function filter($value, string $filterSet = DecodaManager::DECODA_DEFAULT): string
    {
        if (!is_string($value)) {
            throw new RuntimeError('The filter can be applied to strings only.');
        }

        return $this->decodaManager->get($value, $filterSet)->parse();
    }

    /**
     * Strip tags.
     *
     * @param mixed $value
     * @param string $filterSet
     *
     * @return string
     *
     * @throws RuntimeError
     */
    public function clean($value, string $filterSet = DecodaManager::DECODA_DEFAULT): string
    {
        if (!is_string($value)) {
            throw new RuntimeError('The filter can be applied to strings only.');
        }

        return $this->decodaManager->get($value, $filterSet)->strip(true);
    }

    public function getName()
    {
        return 'fm_bbcode';
    }
}
