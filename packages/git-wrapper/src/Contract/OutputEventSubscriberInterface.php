<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Contract;

use Symplify\GitWrapper\Event\GitOutputEvent;

interface OutputEventSubscriberInterface
{
    public function handleOutput(GitOutputEvent $gitOutputEvent): void;
}
