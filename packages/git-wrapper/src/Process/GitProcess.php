<?php

declare(strict_types=1);

namespace Symplify\GitWrapper\Process;

use RuntimeException;
use Symfony\Component\Process\Process;
use Symfony\Contracts\EventDispatcher\Event;
use Symplify\GitWrapper\Event\GitBypassEvent;
use Symplify\GitWrapper\Event\GitErrorEvent;
use Symplify\GitWrapper\Event\GitPrepareEvent;
use Symplify\GitWrapper\Event\GitSuccessEvent;
use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\GitWrapper;

final class GitProcess extends Process
{
    public function __construct(
        private GitWrapper $gitWrapper,
        private GitCommand $gitCommand,
        ?string $cwd = null
    ) {
        // Build the command line options, flags, and arguments.
        $commandLine = $gitCommand->getCommandLine();
        $gitBinary = $gitWrapper->getGitBinary();

        // Support for executing an arbitrary git command.
        if (is_string($commandLine)) {
            $commandLine = explode(' ', $commandLine);
        }

        array_unshift($commandLine, $gitBinary);

        // Resolve the working directory of the Git process. Use the directory
        // in the command object if it exists.
        $cwd = $this->resolveWorkingDirectory($cwd, $gitCommand);

        // Finalize the environment variables, an empty array is converted
        // to null which enherits the environment of the PHP process.
        $env = $gitWrapper->getEnvVars();
        if ($env === []) {
            $env = null;
        }

        parent::__construct($commandLine, $cwd, $env, null, (float) $gitWrapper->getTimeout());
    }

    public function start(?callable $callback = null, array $env = []): void
    {
        $gitPrepareEvent = new GitPrepareEvent($this->gitWrapper, $this, $this->gitCommand);
        $this->dispatchEvent($gitPrepareEvent);

        if (! $this->gitCommand->isBypassed()) {
            parent::start($callback, $env);
        } else {
            $gitBypassEvent = new GitBypassEvent($this->gitWrapper, $this, $this->gitCommand);
            $this->dispatchEvent($gitBypassEvent);
        }
    }

    public function wait(?callable $callback = null): int
    {
        if ($this->gitCommand->isBypassed()) {
            return -1;
        }

        try {
            $exitCode = parent::wait($callback);

            if ($this->isSuccessful()) {
                $gitSuccessEvent = new GitSuccessEvent($this->gitWrapper, $this, $this->gitCommand);
                $this->dispatchEvent($gitSuccessEvent);
            } else {
                $output = $this->getErrorOutput();
                if (trim($output) === '') {
                    $output = $this->getOutput();
                }

                throw new GitException($output);
            }
        } catch (RuntimeException $runtimeException) {
            $gitErrorEvent = new GitErrorEvent($this->gitWrapper, $this, $this->gitCommand);
            $this->dispatchEvent($gitErrorEvent);

            throw new GitException($runtimeException->getMessage(), $runtimeException->getCode(), $runtimeException);
        }

        return $exitCode;
    }

    private function resolveWorkingDirectory(?string $cwd, GitCommand $gitCommand): ?string
    {
        if ($cwd !== null) {
            return $cwd;
        }

        $directory = $gitCommand->getDirectory();
        if ($directory === null) {
            return $cwd;
        }

        $cwd = realpath($directory);
        if ($cwd === false) {
            throw new GitException('Path to working directory could not be resolved: ' . $directory);
        }

        return $cwd;
    }

    private function dispatchEvent(Event $event): void
    {
        $eventDispatcher = $this->gitWrapper->getDispatcher();
        $eventDispatcher->dispatch($event);
    }
}
