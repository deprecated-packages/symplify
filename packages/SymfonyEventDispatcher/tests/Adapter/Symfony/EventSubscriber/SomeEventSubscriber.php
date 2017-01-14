<?php declare(strict_types=1);

namespace Symplify\SymfonyEventDispatcher\Tests\Adapter\Symfony\EventSubscriber;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symplify\SymfonyEventDispatcher\Tests\Adapter\Symfony\Event\SomeEvent;

final class SomeEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents() : array
    {
        return [
            SomeEvent::NAME => 'changeState'
        ];
    }

    public function changeState(SomeEvent $someEvent)
    {
        $someEvent->changeState('on');
    }
}
