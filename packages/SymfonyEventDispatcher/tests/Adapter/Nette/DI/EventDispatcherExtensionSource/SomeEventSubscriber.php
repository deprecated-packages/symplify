<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Nette\DI\EventDispatcherExtensionSource;

use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class SomeEventSubscriber implements EventSubscriberInterface
{
    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return ['subscriber.event' => 'methodName'];
    }

    public function methodName(): void
    {
        throw new Exception('Event was dispatched in subscriber.');
    }
}
