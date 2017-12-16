<?php declare(strict_types=1);

namespace Symplify\GitWrapper\Process;

use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\Process;
use Symplify\GitWrapper\Event\GitErrorEvent;
use Symplify\GitWrapper\Event\GitPrepareEvent;
use Symplify\GitWrapper\Event\GitSuccessEvent;
use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\GitCommand;
use Symplify\GitWrapper\GitWrapper;

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

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(
        GitWrapper $gitWrapper,
        GitCommand $gitCommand,
        EventDispatcherInterface $eventDispatcher,
        ?string $cwd = null
    ) {
        $this->gitWrapper = $gitWrapper;
        $this->gitCommand = $gitCommand;
        $this->eventDispatcher = $eventDispatcher;

        // Build the command line options, flags, and arguments.
        $commandLineItems = array_merge(
            [$gitWrapper->getGitBinary()],
            $gitCommand->getCommandLineItems()
        );

        parent::__construct(
            $commandLineItems,
            $this->resolveCwd($gitCommand, $cwd),
            $this->resolveEnvVars($gitWrapper),
            null,
            (float) $gitWrapper->getTimeout()
        );
    }

    /**
     * {@inheritdoc}
     */
    public function run(?callable $callback = null, array $env = []): int
    {
        try {
            $prepareEvent = new GitPrepareEvent($this->gitWrapper, $this, $this->gitCommand);
            $this->eventDispatcher->dispatch(GitPrepareEvent::class, $prepareEvent);

            parent::run($callback);

            if ($this->isSuccessful()) {
                $successEvent = new GitSuccessEvent($this->gitWrapper, $this, $this->gitCommand);
                $this->eventDispatcher->dispatch(GitSuccessEvent::class, $successEvent);
                return (int) $this->getExitCode();
            }

            $output = $this->getErrorOutput();
            if (trim($output) === '') {
                $output = $this->getOutput();
            }

            throw new RuntimeException($output);
        } catch (RuntimeException $exception) {
            $errorEvent = new GitErrorEvent($this->gitWrapper, $this, $this->gitCommand);
            $this->eventDispatcher->dispatch(GitErrorEvent::class, $errorEvent);

            throw new GitException($exception->getMessage());
        }
    }

    /**
     * Resolve the working directory of the Git process. Use the directory
     * in the command object if it exists.
     */
    private function resolveCwd(GitCommand $gitCommand, ?string $cwd): ?string
    {
        if ($cwd) {
            return $cwd;
        }

        $directory = $gitCommand->getDirectory();
        if ($directory !== null) {
            $cwd = realpath($directory);

            if ($cwd === false) {
                throw new GitException(sprintf(
                    'Path to working directory "%s" could not be resolved.',
                    $directory
                ));
            }
        }

        return $cwd;
    }

    /**
     * Finalize the environment variables, an empty array is converted
     * to null which inherits the environment of the PHP process.
     *
     * @return mixed[]
     */
    private function resolveEnvVars(GitWrapper $gitWrapper): ?array
    {
        $env = $gitWrapper->getEnvVars();
        if (! $env) {
            return null;
        }

        return $env;
    }
}
