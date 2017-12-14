<?php declare(strict_types=1);

namespace Symplify\GitWrapper;

use RuntimeException;
use Symfony\Component\Process\Process;
use Symplify\GitWrapper\Event\GitEvent;
use Symplify\GitWrapper\Event\GitEvents;

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
        $commandLine = trim($binary . ' ' . $gitCommand->getCommandLine());

        // Rather array, so they can be escaped
        $commandLineItems = $gitCommand->getCommandLineItems();
        $commandLineItems = array_merge(
            [$binary],
            [$gitCommand->getCommand()],
            $gitCommand->buildOptionsToArray(),
            $gitCommand->getArgs()
        );

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
        // to null which inherits the environment of the PHP process.
        $env = $gitWrapper->getEnvVars();
        if (! $env) {
            $env = null;
        }

        parent::__construct(
            $commandLineItems,
            $cwd,
            $env,
            null,
            (float) $gitWrapper->getTimeout()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function run(?callable $callback = null, array $env = []): int
    {
        $event = new GitEvent($this->gitWrapper, $this, $this->gitCommand);
        $dispatcher = $this->gitWrapper->getDispatcher();

        try {
            $dispatcher->dispatch(GitEvents::GIT_PREPARE, $event);

            parent::run($callback);
            if ($this->isSuccessful()) {
                $dispatcher->dispatch(GitEvents::GIT_SUCCESS, $event);
                return $this->getExitCode();
            }

            $output = $this->getErrorOutput();
            if (trim($output) === '') {
                $output = $this->getOutput();
            }

            throw new RuntimeException($output);

        } catch (RuntimeException $exception) {
            $dispatcher->dispatch(GitEvents::GIT_ERROR, $event);
            throw new GitException($exception->getMessage());
        }
    }
}
