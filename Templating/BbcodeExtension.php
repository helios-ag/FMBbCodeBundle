<?php

namespace FM\BbcodeBundle\Templating;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\Error\RuntimeError;
use FM\BbcodeBundle\Decoda\DecodaManager as DecodaManager;

/**
 * @author Al Ganiev <helios.ag@gmail.com>
 * @copyright 2012-2015 Al Ganiev
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
class BbcodeExtension extends AbstractExtension
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
     * Returns the available filters.
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        $options = ['is_safe' => ['html']];

        return [
            new TwigFilter('bbcode_filter', [$this, 'filter'], $options),
            new TwigFilter('bbcode_clean', [$this, 'clean'], $options),
        ];
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
}
