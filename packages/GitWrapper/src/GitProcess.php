<?php declare(strict_types=1);

namespace Symplify\GitWrapper;

use RuntimeException;
use Symfony\Component\Process\Process;
use Symplify\GitWrapper\Event\GitEvent;
use Symplify\GitWrapper\Event\GitEvents;

/**
 * GitProcess runs a Git command in an independent process.
 */
final class GitProcess extends Process
{
    /**
     * @var GitWrapper
     */
    private $gitWrapper;

    /**
     * @var GitCommand
     */
    private $gitCommand;

    public function __construct(GitWrapper $gitWrapper, GitCommand $gitCommand, ?string $cwd = null)
    {
        $this->gitWrapper = $gitWrapper;
        $this->gitCommand = $gitCommand;

        // Build the command line options, flags, and arguments.
        $binary = $gitWrapper->getGitBinary();
        $commandLine = rtrim($binary . ' ' . $gitCommand->getCommandLine());

        // Resolve the working directory of the Git process. Use the directory
        // in the command object if it exists.
        if ($cwd === null) {
            $directory = $gitCommand->getDirectory();
            if ($directory !== null) {
                if (! $cwd = realpath($directory)) {
                    throw new GitException('Path to working directory could not be resolved: ' . $directory);
                }
            }
        }

        // Finalize the environment variables, an empty array is converted
        // to null which enherits the environment of the PHP process.
        $env = $gitWrapper->getEnvVars();
        if (! $env) {
            $env = null;
        }

        parent::__construct($commandLine, $cwd, $env, null, (float) $gitWrapper->getTimeout());
    }

    /**
     * {@inheritdoc}
     */
    public function run(?callable $callback = null, array $env = []): int
    {
        $event = new GitEvent($this->gitWrapper, $this, $this->gitCommand);
        $dispatcher = $this->gitWrapper->getDispatcher();

        try {
            // Throw the "git.command.prepare" event prior to executing.
            $dispatcher->dispatch(GitEvents::GIT_PREPARE, $event);

            // Execute command if it is not flagged to be bypassed and throw the
            // "git.command.success" event, otherwise do not execute the comamnd
            // and throw the "git.command.bypass" event.
            if ($this->gitCommand->notBypassed()) {
                $status = parent::run($callback);

                if ($this->isSuccessful()) {
                    $dispatcher->dispatch(GitEvents::GIT_SUCCESS, $event);
                } else {
                    $output = $this->getErrorOutput();

                    if (trim($output) === '') {
                        $output = $this->getOutput();
                    }

                    throw new RuntimeException($output);
                }
                return $status;
            }

            $dispatcher->dispatch(GitEvents::GIT_BYPASS, $event);
            // success code
            return 0;

        } catch (RuntimeException $exception) {
            $dispatcher->dispatch(GitEvents::GIT_ERROR, $event);
            throw new GitException($exception->getMessage());
        }
    }
}
