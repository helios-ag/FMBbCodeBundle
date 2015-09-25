<?php

namespace FM\BbcodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterHooksPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fm_bbcode.decoda_manager')) {
            return;
        }

        $hooks = $container->getParameter('fm_bbcode.config.hooks');

        $definitions = array();

        foreach ($hooks as $id => $class) {
            $definition = new Definition($class);
            $definition->addTag('fm_bbcode.decoda.hook', array(
                'id' => $id,
            ));
            $definitions['fm_bbcode.decoda.hook.from_config.'.$id] = $definition;
        }

        $container->addDefinitions($definitions);

        $definition = $container->getDefinition('fm_bbcode.decoda_manager');

        $hooks = array();
        foreach ($container->findTaggedServiceIds('fm_bbcode.decoda.hook') as $id => $attributes) {
            // We must assume that the class value has been correctly filled, even if the service is created by a factory
            $class = $container->getDefinition($id)->getClass();

            $refClass  = new \ReflectionClass($class);
            $interface = 'Decoda\Hook';
            if (!$refClass->implementsInterface($interface)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, $interface));
            }

            $name = isset($attributes[0]['id']) ? $attributes[0]['id'] : $id;
            $name = strtolower($name);
            if (isset($hooks[$name])) {
                throw new \InvalidArgumentException(sprintf('The hook identifier "%s" must be uniq, is already set on "%s" service.', $name, $hooks[$name]));
            }

            $hooks[$name] = $id;

            $definition->addMethodCall('setHook', array($name, new Reference($id)));
        }
    }
}
