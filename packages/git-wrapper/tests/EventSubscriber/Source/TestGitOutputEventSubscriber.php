<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\EventSubscriber\Source;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\GitWrapper\Contract\OutputEventSubscriberInterface;
use Symplify\GitWrapper\Event\GitOutputEvent;

final class TestGitOutputEventSubscriber implements EventSubscriberInterface, OutputEventSubscriberInterface
{
    /**
     * @var GitOutputEvent
     */
    private $gitOutputEvent;

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
        $this->gitOutputEvent = $gitOutputEvent;
    }

    /**
     * For testing
     */
    public function getLastEvent(): GitOutputEvent
    {
        return $this->gitOutputEvent;
    }
}
