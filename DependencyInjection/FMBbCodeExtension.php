<?php

namespace FM\BbCodeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
* Registration of the extension via DI.
*
* @author Al Ganiev <helios.ag@gmail.com>
* @copyright 2011 Al Ganiev
* @license http://www.opensource.org/licenses/mit-license.php MIT License
*/
class FMBbCodeExtension extends Extension {

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->process($configuration->getConfigTree(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('bbcode.xml');
        
        $container->setParameter('fm_bb_code.locale', $config['locale']);

        $container->setParameter('fm_bb_code.filters.default', $config['filters']['default']);
        $container->setParameter('fm_bb_code.filters.block', $config['filters']['block']);
        $container->setParameter('fm_bb_code.filters.code', $config['filters']['code']);
        $container->setParameter('fm_bb_code.filters.email', $config['filters']['email']);
        $container->setParameter('fm_bb_code.filters.image', $config['filters']['image']);
        $container->setParameter('fm_bb_code.filters.list', $config['filters']['list']);
        $container->setParameter('fm_bb_code.filters.quote', $config['filters']['quote']);
        $container->setParameter('fm_bb_code.filters.text', $config['filters']['text']);
        $container->setParameter('fm_bb_code.filters.url', $config['filters']['url']);
        $container->setParameter('fm_bb_code.filters.video', $config['filters']['video']);
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension.ExtensionInterface::getAlias()
     * @codeCoverageIgnore
     */
    function getAlias()
    {
        return 'fm_bb_code';
    }

}