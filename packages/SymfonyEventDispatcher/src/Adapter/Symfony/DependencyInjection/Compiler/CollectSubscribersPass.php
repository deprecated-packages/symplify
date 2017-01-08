<?php declare(strict_types = 1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\DependencyInjectionUtils\Adapter\Symfony\DependencyInjection\CollectorTrait;

final class CollectSubscribersPass implements CompilerPassInterface
{
    use CollectorTrait;

    public function process(ContainerBuilder $containerBuilder)
    {
        $this->loadCollectorWithType(
            $containerBuilder,
            EventDispatcherInterface::class,
            EventSubscriberInterface::class,
            'addSubscriber'
        );
    }
}
