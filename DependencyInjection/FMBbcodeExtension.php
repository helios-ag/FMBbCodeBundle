<?php

namespace FM\BbcodeBundle\DependencyInjection;

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
class FMBbcodeExtension extends Extension
{
    /**
     * @see Symfony\Component\DependencyInjection\Extension.ExtensionInterface::load()
     * @param array                                                   $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('bbcode.xml');
        $hooks = isset($config['config']['hooks']) ? $config['config']['hooks'] : array();
        $filters = isset($config['config']['filters']) ? $config['config']['filters'] : array();
        $container->setParameter('fm_bbcode.filter_sets', $config['filter_sets']);
        $container->setParameter('fm_bbcode.config.filters', $filters);
        $container->setParameter('fm_bbcode.config.hooks', $hooks);
        $container->setParameter('fm_bbcode.config.messages', isset($config['config']['messages']) ? $config['config']['messages'] : '');
        $container->setParameter('fm_bbcode.config.templates', isset($config['config']['templates']) ? $config['config']['templates'] : array());
        $container->setParameter('fm_bbcode.config.emoticonpath', $config['config']['emoticonpath']);
        $container->setParameter('fm_bbcode.config.extraemoticonpath', $config['config']['extraemoticonpath']);
    }
}
