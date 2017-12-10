<?php declare(strict_types=1);

namespace GitWrapper\Event;

/**
 * Interface implemented by output listeners.
 */
interface GitOutputListenerInterface
{
    public function handleOutput(GitOutputEvent $event): void;
}
