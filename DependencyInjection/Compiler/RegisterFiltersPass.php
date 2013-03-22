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

        $filters = array();
        foreach ($container->findTaggedServiceIds('fm_bbcode.decoda.filter') as $id => $attributes) {
            $name = isset($attributes[0]['id']) ? $attributes[0]['id'] : $id;
            $name = strtolower($name);
            if (isset($filters[$name])) {
                throw new \InvalidArgumentException(sprintf(
                    'The filter identifier "%s" must be uniq.',
                    $name
                ));
            }
            $filters[$name] = new Reference($id);
        }

        $container->getDefinition('fm_bbcode.decoda_manager')->addMethodCall('addFilters', array($filters));
    }
}
