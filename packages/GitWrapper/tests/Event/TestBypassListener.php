<?php declare(strict_types=1);

namespace GitWrapper\Test\Event;

use GitWrapper\Event\GitEvent;

final class TestBypassListener
{
    public function onPrepare(GitEvent $event): void
    {
        $event->getCommand()->bypass();
    }
}
