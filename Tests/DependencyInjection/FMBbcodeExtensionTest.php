<?php

/*
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FM\BbcodeBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use FM\BbcodeBundle\DependencyInjection\FMBbcodeExtension;
use Symfony\Component\Yaml\Parser;

class FMBbcodeExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $containerBuilder;

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testUserLoadThrowsExceptionUnlessDriverIsValid()
    {
        $loader = new FMBbcodeExtension();
        $config = array('filter_sets' => array('foo' => array('filters' => 'default')));
        $loader->load(array($config), new ContainerBuilder());
    }

    /**
     * @return ContainerBuilder
     */
    protected function createEmptyConfiguration()
    {
        $this->containerBuilder = new ContainerBuilder();
        $loader                 = new FMBbcodeExtension();
        $loader->load(array(array()), $this->containerBuilder);
        $this->assertTrue($this->containerBuilder instanceof ContainerBuilder);
    }

    /**
     * @return ContainerBuilder
     */
    protected function createFullConfiguration()
    {
        $this->containerBuilder = new ContainerBuilder();
        $loader                 = new FMBbcodeExtension();
        $loader->load(array($this->getFullConfig()), $this->containerBuilder);
        $this->assertTrue($this->containerBuilder instanceof ContainerBuilder);
    }

    protected function getFullConfig()
    {
        $yaml = <<<EOF
filter_sets:
    forum_post_filter:
        locale: ru
        xhtml: true
        filters: [ default, quote ]
        whitelist: [ b, u, i, list, quote, code, text, url, email, image, video ]
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    private function assertAlias($value, $key)
    {
        $this->assertEquals($value, (string) $this->containerBuilder->getAlias($key), sprintf('%s alias is correct', $key));
    }

    private function assertParameter($value, $key)
    {
        $this->assertEquals($value, $this->containerBuilder->getParameter($key), sprintf('%s parameter is correct', $key));
    }

    private function assertHasDefinition($id)
    {
        $this->assertTrue(($this->containerBuilder->hasDefinition($id) ?: $this->containerBuilder->hasAlias($id)));
    }

    private function assertNotHasDefinition($id)
    {
        $this->assertFalse(($this->containerBuilder->hasDefinition($id) ?: $this->containerBuilder->hasAlias($id)));
    }

    private function assertDICConstructorArguments($definition, $args)
    {
        $this->assertEquals($args, $definition->getArguments(), "Expected and actual DIC Service constructor arguments of definition '".$definition->getClass()."' don't match.");
    }

    protected function tearDown()
    {
        unset($this->containerBuilder);
    }
}
