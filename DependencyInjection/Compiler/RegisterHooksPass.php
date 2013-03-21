<?php

namespace FM\BbcodeBundle\DependencyInjection\Compiler;

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

        $hooks = array();
        foreach ($container->findTaggedServiceIds('fm_bbcode.decoda.hook') as $id => $attributes) {
            $name = isset($attributes[0]['id']) ? $attributes[0]['id'] : $id;
            $name = strtolower($name);
            if (isset($filters[$name]) || $name == null) {
                throw new \InvalidArgumentException(sprintf(
                    'The hook identifier "%s" must be uniq.',
                    $name
                ));
            }
            $hooks[$name] = new Reference($id);
        }

        $container->getDefinition('fm_bbcode.decoda_manager')->addMethodCall('addHooks', array($hooks));
    }
}
