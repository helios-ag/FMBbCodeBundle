<?php

namespace FM\BbcodeBundle;

use FM\BbcodeBundle\DependencyInjection\Compiler\RegisterHooksPass;
use FM\BbcodeBundle\DependencyInjection\Compiler\RegisterFiltersPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FMBbcodeBundle extends Bundle
{
    /**
     * @see Bundle::build()
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterFiltersPass(), PassConfig::TYPE_AFTER_REMOVING);
        $container->addCompilerPass(new RegisterHooksPass(), PassConfig::TYPE_AFTER_REMOVING);
    }
}
