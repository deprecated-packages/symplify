<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Event;

/**
 * Event handler that streams real-time output from Git commands to STDOUT and
 * STDERR.
 */
final class GitOutputStreamListener implements GitOutputListenerInterface
{
    public function handleOutput(GitOutputEvent $gitOutputEvent): void
    {
        $handler = $gitOutputEvent->isError() ? STDERR : STDOUT;
        fputs($handler, $gitOutputEvent->getBuffer());
    }
}
