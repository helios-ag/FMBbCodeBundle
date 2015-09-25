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
     *
     * @param array                                                   $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('bbcode.xml');
        $loader->load('filters.xml');
        $loader->load('hooks.xml');

        $hooksConfig = isset($config['config']['hooks']) ? $config['config']['hooks'] : array();
        $hooks       = array();
        foreach ($hooksConfig as $hook) {
            $hooks[$hook['classname']] = $hook['class'];
        }

        $filtersConfig = isset($config['config']['filters']) ? $config['config']['filters'] : array();
        $filters       = array();
        foreach ($filtersConfig as $filter) {
            $filters[$filter['classname']] = $filter['class'];
        }

        $container->setParameter('fm_bbcode.filter_sets', $config['filter_sets']);
        $container->setParameter('fm_bbcode.config.filters', $filters);
        $container->setParameter('fm_bbcode.config.hooks', $hooks);
        $container->setParameter('fm_bbcode.config.messages', isset($config['config']['messages']) ? $config['config']['messages'] : null);
        $container->setParameter('fm_bbcode.config.templates', isset($config['config']['templates']) ? $config['config']['templates'] : array());

        if (isset($config['emoticon'])) {
            $this->registerEmoticonConfiguration($config['emoticon'], $container, $loader);
        }
    }

    /**
     * Loads the emoticon configuration.
     *
     * @param array            $config    A router configuration array
     * @param ContainerBuilder $container A ContainerBuilder instance
     * @param XmlFileLoader    $loader    An XmlFileLoader instance
     */
    private function registerEmoticonConfiguration(array $config, ContainerBuilder $container, XmlFileLoader $loader)
    {
        $container->setParameter('fm_bbcode.emoticon.cache_class_prefix', $container->getParameter('kernel.name').ucfirst($container->getParameter('kernel.environment')));
        $container->setParameter('fm_bbcode.emoticon.folder', $config['folder']);

        $hook     = $container->findDefinition('fm_bbcode.decoda.hook.emoticon');
        $argument = $hook->getArgument(2);

        if (isset($config['resource'])) {
            $argument['resource'] = $config['resource'];
        }

        if (isset($config['type'])) {
            $argument['resource_type'] = $config['type'];
        }

        if (isset($config['path'])) {
            $argument['path'] = $config['path'];
            $container->setParameter('fm_bbcode.emoticon.path', $config['path']);
        }

        if (isset($config['extension'])) {
            $argument['extension'] = $config['extension'];
        }

        $hook->replaceArgument(2, $argument);
    }
}
