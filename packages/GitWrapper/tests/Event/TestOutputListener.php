<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\Event;

use Symplify\GitWrapper\Event\GitOutputEvent;
use Symplify\GitWrapper\Event\GitOutputListenerInterface;

final class TestOutputListener implements GitOutputListenerInterface
{
    /**
     * @var GitOutputEvent
     */
    private $gitOutputEvent;

    public function getLastEvent(): GitOutputEvent
    {
        return $this->gitOutputEvent;
    }

    public function handleOutput(GitOutputEvent $gitOutputEvent): void
    {
        $this->gitOutputEvent = $gitOutputEvent;
    }
}
