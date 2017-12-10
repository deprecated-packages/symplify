<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Tests\Event;

use Symplify\GitWrapper\Event\GitEvent;

final class TestBypassListener
{
    public function onPrepare(GitEvent $gitEvent): void
    {
        $gitEvent->getCommand()->bypass();
    }
}
