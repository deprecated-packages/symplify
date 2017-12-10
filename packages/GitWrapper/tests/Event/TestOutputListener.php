<?php declare(strict_types=1);

namespace GitWrapper\Test\Event;

use GitWrapper\Event\GitOutputEvent;
use GitWrapper\Event\GitOutputListenerInterface;

final class TestOutputListener implements GitOutputListenerInterface
{
    /**
     * @var \GitWrapper\Event\GitOutputEvent
     */
    private $event;

    
    public function getLastEvent(): GitWrapper\Event\GitOutputEvent
    {
        return $this->event;
    }

    public function handleOutput(GitOutputEvent $event): void
    {
        $this->event = $event;
    }
}
