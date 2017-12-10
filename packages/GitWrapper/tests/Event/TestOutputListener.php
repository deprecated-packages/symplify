<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\Event;

use GitWrapper\Event\GitOutputEvent as GitWrapperGitOutputEvent;
use Symplify\GitWrapper\Event\GitOutputEvent;
use Symplify\GitWrapper\Event\GitOutputListenerInterface;

final class TestOutputListener implements GitOutputListenerInterface
{
    /**
     * @var \Symplify\GitWrapper\Event\GitOutputEvent
     */
    private $gitOutputEvent;

    public function getLastEvent(): GitWrapperGitOutputEvent
    {
        return $this->gitOutputEvent;
    }

    public function handleOutput(GitOutputEvent $gitOutputEvent): void
    {
        $this->gitOutputEvent = $gitOutputEvent;
    }
}
