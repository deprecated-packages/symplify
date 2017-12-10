<?php declare(strict_types=1);

namespace Symplify\GitWrapper\EventListener;

use Symplify\GitWrapper\Contract\EventListener\GitOutputListenerInterface;
use Symplify\GitWrapper\Event\GitOutputEvent;

/**
 * Event handler that streams real-time output from Git commands to STDOUT and
 * STDERR.
 */
final class GitOutputStreamListener implements GitOutputListenerInterface
{
    public function handleOutput(GitOutputEvent $gitOutputEvent): void
    {
        $handler = $gitOutputEvent->isError() ? STDERR : STDOUT;
        fwrite($handler, $gitOutputEvent->getBuffer());
    }
}
