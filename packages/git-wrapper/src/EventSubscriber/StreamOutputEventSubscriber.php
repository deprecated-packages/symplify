<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\GitWrapper\Contract\OutputEventSubscriberInterface;
use Symplify\GitWrapper\Event\GitOutputEvent;

/**
 * Event handler that streams real-time output from Git commands to STDOUT and STDERR.
 */
final class StreamOutputEventSubscriber implements EventSubscriberInterface, OutputEventSubscriberInterface
{
    /**
     * @return array<string, string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GitOutputEvent::class => 'handleOutput',
        ];
    }

    public function handleOutput(GitOutputEvent $gitOutputEvent): void
    {
        $handler = $gitOutputEvent->isError() ? STDERR : STDOUT;
        fwrite($handler, $gitOutputEvent->getBuffer());
    }
}
