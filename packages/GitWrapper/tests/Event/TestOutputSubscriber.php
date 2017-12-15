<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\Event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symplify\GitWrapper\Event\GitEvents;
use Symplify\GitWrapper\Event\GitOutputEvent;

final class TestOutputSubscriber implements EventSubscriberInterface
{
    /**
     * @var GitOutputEvent
     */
    private $gitOutputEvent;

    /**
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            GitEvents::GIT_OUTPUT => 'handleOutput'
        ];
    }

    public function handleOutput(GitOutputEvent $gitOutputEvent): void
    {
        $this->gitOutputEvent = $gitOutputEvent;
    }

    public function getLastEvent(): GitOutputEvent
    {
        return $this->gitOutputEvent;
    }
}
