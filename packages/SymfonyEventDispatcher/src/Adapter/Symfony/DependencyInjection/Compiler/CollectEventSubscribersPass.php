<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Adapter\Symfony\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\PackageBuilder\Adapter\Symfony\DependencyInjection\DefinitionFinder;

final class CollectEventSubscribersPass implements CompilerPassInterface
{
    /**
     * @var string
     */
    private const EVENT_SUBSCRIBER_TAG = 'kernel.event_subscriber';

    public function process(ContainerBuilder $containerBuilder): void
    {
        $eventSubscribers = DefinitionFinder::findAllByType($containerBuilder, EventSubscriberInterface::class);
        foreach ($eventSubscribers as $eventSubscriber) {
            if (! $eventSubscriber->hasTag(self::EVENT_SUBSCRIBER_TAG)) {
                $eventSubscriber->addTag(self::EVENT_SUBSCRIBER_TAG);
            }
        }
    }
}
