<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Test\Event;

use Symplify\GitWrapper\Event\GitEvent;

final class TestBypassListener
{
    public function onPrepare(GitEvent $event): void
    {
        $event->getCommand()->bypass();
    }
}
