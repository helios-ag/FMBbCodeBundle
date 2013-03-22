<?php

namespace FM\BbcodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class RegisterFiltersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fm_bbcode.decoda_manager')) {
            return;
        }

        $definition = $container->getDefinition('fm_bbcode.decoda_manager');

        $filters = array();
        foreach ($container->findTaggedServiceIds('fm_bbcode.decoda.filter') as $id => $attributes) {
            // We must assume that the class value has been correctly filled, even if the service is created by a factory
            $class = $container->getDefinition($id)->getClass();

            $refClass = new \ReflectionClass($class);
            $interface = 'Decoda\Filter';
            if (!$refClass->implementsInterface($interface)) {
                throw new \InvalidArgumentException(sprintf('Service "%s" must implement interface "%s".', $id, $interface));
            }

            $name = isset($attributes[0]['id']) ? $attributes[0]['id'] : $id;
            $name = strtolower($name);
            if (isset($filters[$name])) {
                throw new \InvalidArgumentException(sprintf('The hook identifier "%s" must be uniq, is already set on "%s" service.', $name, $filters[$name]));
            }

            $filters[$name] = $id;

            $definition->addMethodCall('setFilter', array($name, new Reference($id)));
        }
    }
}
