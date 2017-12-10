<?php declare(strict_types=1);

namespace GitWrapper\Event;

/**
 * Event handler that streams real-time output from Git commands to STDOUT and
 * STDERR.
 */
final class GitOutputStreamListener implements GitOutputListenerInterface
{
    public function handleOutput(GitOutputEvent $event): void
    {
        $handler = $event->isError() ? STDERR : STDOUT;
        fputs($handler, $event->getBuffer());
    }
}
