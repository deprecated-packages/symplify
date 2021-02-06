<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\EventSubscriber\Source;

use Symplify\GitWrapper\Event\GitOutputEvent;
use Symplify\GitWrapper\EventSubscriber\AbstractOutputEventSubscriber;

final class TestGitOutputEventSubscriber extends AbstractOutputEventSubscriber
{
    /**
     * @var GitOutputEvent
     */
    private $gitOutputEvent;

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
