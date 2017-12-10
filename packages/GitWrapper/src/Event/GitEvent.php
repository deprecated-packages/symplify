<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\GitWrapper;

/**
 * Event instance passed as a result of git.* commands.
 */
class GitEvent extends Event
{
    /**
     * The GitWrapper object that likely instantiated this class.
     *
     * @var \Symplify\GitWrapper\GitWrapper
     */
    protected $gitWrapper;

    /**
     * The Process object being run.
     *
     * @var \Symfony\Component\Process\Process
     */
    protected $process;

    /**
     * The GitCommand object being executed.
     *
     * @var \Symplify\GitWrapper\GitCommand
     */
    protected $gitCommand;

    public function __construct(GitWrapper $gitWrapper, Process $process, GitCommand $gitCommand)
    {
        $this->gitWrapper = $gitWrapper;
        $this->process = $process;
        $this->gitCommand = $gitCommand;
    }

    /**
     * Gets the GitWrapper object that likely instantiated this class.
     */
    public function getWrapper(): GitWrapper
    {
        return $this->gitWrapper;
    }

    /**
     * Gets the Process object being run.
     */
    public function getProcess(): Process
    {
        return $this->process;
    }

    /**
     * Gets the GitCommand object being executed.
     */
    public function getCommand(): GitCommand
    {
        return $this->gitCommand;
    }
}
