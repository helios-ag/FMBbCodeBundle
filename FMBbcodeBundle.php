<?php

namespace FM\BbcodeBundle;

use FM\BbcodeBundle\DependencyInjection\Compiler\RegisterHooksPass;
use FM\BbcodeBundle\DependencyInjection\Compiler\RegisterFiltersPass;

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
         $container->addCompilerPass(new RegisterFiltersPass());
         $container->addCompilerPass(new RegisterHooksPass());
    }
}
