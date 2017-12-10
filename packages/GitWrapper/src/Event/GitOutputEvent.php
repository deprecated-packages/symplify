<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Event;

use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\GitWrapper;
use Symfony\Component\Process\Process;

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
     * @param GitWrapper $wrapper The GitWrapper object that likely instantiated this class.
     * @param \Symfony\Component\Process\Process $process The Process object being run.
     * @param \GitWrapper\GitCommand $command The GitCommand object being executed.
     */
    public function __construct(GitWrapper $wrapper, Process $process, GitCommand $command, $type, $buffer)
    {
        parent::__construct($wrapper, $process, $command);
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
        return (Process::ERR == $this->type);
    }
}
