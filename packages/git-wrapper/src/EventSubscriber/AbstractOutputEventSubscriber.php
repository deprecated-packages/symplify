<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\GitWrapper\Contract\OutputEventSubscriberInterface;
use Symplify\GitWrapper\Event\GitOutputEvent;

abstract class AbstractOutputEventSubscriber implements EventSubscriberInterface, OutputEventSubscriberInterface
{
    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GitOutputEvent::class => 'handleOutput',
        ];
    }
}
