<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\EasyHydrator\DependencyInjection\Extension\EasyHydratorExtension;

final class EasyHydratorBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }

    protected function createContainerExtension(): EasyHydratorExtension
    {
        return new EasyHydratorExtension();
    }
}
