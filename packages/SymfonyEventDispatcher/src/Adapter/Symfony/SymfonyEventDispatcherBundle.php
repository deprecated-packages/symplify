<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Symfony;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\SymfonyEventDispatcher\Adapter\Symfony\DependencyInjection\Compiler\CollectEventSubscribersPass;
use Symplify\SymfonyEventDispatcher\Adapter\Symfony\DependencyInjection\Extension\ContainerExtension;

final class SymfonyEventDispatcherBundle extends Bundle
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectEventSubscribersPass, PassConfig::TYPE_BEFORE_OPTIMIZATION);
    }

    public function getContainerExtension(): ContainerExtension
    {
        return new ContainerExtension;
    }
}
