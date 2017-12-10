<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Process\Process;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\GitWrapper;

/**
 * Event instance passed as a result of git.* commands.
 */
abstract class AbstractGitEvent extends Event
{
    /**
     * The GitWrapper object that likely instantiated this class.
     *
     * @var \Symplify\GitWrapper\GitWrapper
     */
    protected $\Symplify\GitWrapper\GitWrapper;

    /**
     * The Process object being run.
     *
     * @var \Symfony\Component\Process\Process
     */
    protected $\Symfony\Component\Process\Process;

    /**
     * The GitCommand object being executed.
     *
     * @var \Symplify\GitWrapper\GitCommand
     */
    protected $\Symplify\GitWrapper\GitCommand;

    public function __construct(GitWrapper $gitWrapper, Process $process, GitCommand $gitCommand)
    {
        $this->\Symplify\GitWrapper\GitWrapper = $gitWrapper;
        $this->\Symfony\Component\Process\Process = $process;
        $this->\Symplify\GitWrapper\GitCommand = $gitCommand;
    }

    /**
     * Gets the GitWrapper object that likely instantiated this class.
     */
    public function getWrapper(): GitWrapper
    {
        return $this->\Symplify\GitWrapper\GitWrapper;
    }

    /**
     * Gets the Process object being run.
     */
    public function getProcess(): Process
    {
        return $this->\Symfony\Component\Process\Process;
    }

    /**
     * Gets the GitCommand object being executed.
     */
    public function getCommand(): GitCommand
    {
        return $this->\Symplify\GitWrapper\GitCommand;
    }
}
