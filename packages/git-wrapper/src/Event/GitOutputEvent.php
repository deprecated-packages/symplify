<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Event;

use Symfony\Component\Process\Process;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\GitWrapper;

/**
 * Event instance passed when output is returned from Git commands.
 */
final class GitOutputEvent extends AbstractGitEvent
{
    public function __construct(
        GitWrapper $gitWrapper,
        Process $process,
        GitCommand $gitCommand,
        private string $type,
        private string $buffer
    ) {
        parent::__construct($gitWrapper, $process, $gitCommand);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getBuffer(): string
    {
        return $this->buffer;
    }

    public function isError(): bool
    {
        return $this->type === Process::ERR;
    }
}
