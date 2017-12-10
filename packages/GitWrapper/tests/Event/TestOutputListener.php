<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Test\Event;

use Symplify\GitWrapper\Event\GitOutputEvent;
use Symplify\GitWrapper\Event\GitOutputListenerInterface;

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
