<?php

namespace FM\BbcodeBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2012 Christian Raue
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
abstract class TwigBasedTestCase extends WebTestCase
{
    /**
     * @var \Twig_Environment
     */
    protected $twig;

    protected static function createKernel(array $options = array())
    {
        return new AppKernel(isset($options['config']) ? $options['config'] : 'config.yml');
    }

    protected function setUp()
    {
        static::createClient();
        $this->twig = self::$kernel->getContainer()->get('twig');
    }

    protected function getTwig()
    {
        return $this->twig;
    }

}
