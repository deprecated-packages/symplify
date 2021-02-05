<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Event;

use Symfony\Component\Process\Process;
use Symfony\Contracts\EventDispatcher\Event;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\GitWrapper;

/**
 * Event instance passed as a result of git.* commands.
 */
abstract class AbstractGitEvent extends Event
{
    /**
     * @var GitWrapper
     */
    protected $gitWrapper;

    /**
     * @var Process
     */
    protected $process;

    /**
     * @var GitCommand
     */
    protected $gitCommand;

    public function __construct(GitWrapper $gitWrapper, Process $process, GitCommand $gitCommand)
    {
        $this->gitWrapper = $gitWrapper;
        $this->process = $process;
        $this->gitCommand = $gitCommand;
    }

    public function getWrapper(): GitWrapper
    {
        return $this->gitWrapper;
    }

    public function getProcess(): Process
    {
        return $this->process;
    }

    public function getCommand(): GitCommand
    {
        return $this->gitCommand;
    }
}
