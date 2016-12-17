<?php

namespace Symplify\SymfonyEventDispatcher\Tests\DI\EventDispatcherExtensionSource;

use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return ['subscriber.event' => 'methodName'];
    }

    public function methodName()
    {
        throw new Exception('Event was dispatched in subscriber.');
    }
}
