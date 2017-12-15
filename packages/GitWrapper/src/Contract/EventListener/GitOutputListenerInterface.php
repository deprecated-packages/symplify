<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Contract\EventListener;

use Symplify\GitWrapper\Event\GitOutputEvent;

/**
 * Interface implemented by output listeners.
 */
interface GitOutputListenerInterface
{
    public function handleOutput(GitOutputEvent $gitOutputEvent): void;
}
