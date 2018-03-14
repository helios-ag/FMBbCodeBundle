<?php

namespace FM\BbcodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Decoda\Filter;

class RegisterFiltersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fm_bbcode.decoda_manager')) {
            return;
        }

        $filters = $container->getParameter('fm_bbcode.config.filters');

        $definitions = array();

        foreach ($filters as $id => $class) {
            $definition = new Definition($class);
            $definition->addTag('fm_bbcode.decoda.filter', array(
                'id' => $id,
            ));
            $definitions['fm_bbcode.decoda.filter.from_config.'.$id] = $definition;
        }

        $container->addDefinitions($definitions);

        $definition = $container->getDefinition('fm_bbcode.decoda_manager');

        $filters = array();
        foreach ($container->findTaggedServiceIds('fm_bbcode.decoda.filter') as $id => $attributes) {
            // We must assume that the class value has been correctly filled, even if the service is created by a factory
            $class = $container->getDefinition($id)->getClass();

            $refClass  = new \ReflectionClass($class);
            $interface = Filter::class;
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
