<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Event;

use Symfony\Component\Process\Process;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\GitWrapper;

/**
 * Event instance passed when output is returned from Git commands.
 */
final class GitOutputEvent extends GitEvent
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $buffer;

    /**
     * Constructs a GitEvent object.
     *
     * @param Symfony\Component\Process\Process $process The Process object being run.
     */
    public function __construct(GitWrapper $gitWrapper, Process $process, GitCommand $gitCommand, $type, $buffer)
    {
        parent::__construct($gitWrapper, $process, $gitCommand);
        $this->type = $type;
        $this->buffer = $buffer;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getBuffer(): string
    {
        return $this->buffer;
    }

    /**
     * Tests wheter the buffer was captured from STDERR.
     */
    public function isError()
    {
        return $this->type === Process::ERR;
    }
}
