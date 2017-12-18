<?php declare(strict_types=1);

namespace Symplify\GitWrapper;

use Nette\Utils\Strings;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symplify\GitWrapper\Event\GitOutputEvent;
use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\Naming\NameParser;
use Symplify\GitWrapper\Process\GitProcessFactory;

/**
 * A wrapper class around the Git binary.
 *
 * Contains necessary context to run Git commands - the path to the Git binary, environment variables,
 * helper methods to run Git commands and set up the connection to the GIT_SSH wrapper script
 */
final class GitWrapper
{
    /**
     * @var string
     */
    public const ENV_GIT_SSH = 'GIT_SSH';

    /**
     * @var string
     */
    public const ENV_GIT_SSH_KEY = 'GIT_SSH_KEY';

    /**
     * @var string
     */
    public const ENV_GIT_SSH_PORT = 'GIT_SSH_PORT';

    /**
     * @var string
     */
    private $gitBinary;

    /**
     * Environment variables defined in the scope of the Git command.
     *
     * @var mixed[]
     */
    private $env = [];

    /**
     * The timeout of the Git command in seconds.
     *
     * @var int
     */
    private $timeout = 60;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var GitProcessFactory
     */
    private $gitProcessFactory;

    /**
     * @var NameParser
     */
    private $nameParser;

    public function __construct(
        ?string $gitBinary = null,
        EventDispatcherInterface $eventDispatcher,
        ExecutableFinder $executableFinder,
        GitProcessFactory $gitProcessFactory,
        NameParser $nameParser
    ) {
        if ($gitBinary === null) {
            $gitBinary = $executableFinder->find('git');
            if (! $gitBinary) {
                throw new GitException('Unable to find the Git executable.');
            }
        }

        $this->gitBinary = $gitBinary;
        $this->eventDispatcher = $eventDispatcher;
        $this->gitProcessFactory = $gitProcessFactory;
        $this->nameParser = $nameParser;
    }

    public function getGitBinary(): string
    {
        return $this->gitBinary;
    }

    public function setEnvVar(string $var, $value): void
    {
        $this->env[$var] = $value;
    }

    /**
     * @param mixed $default The value returned if the environment variable is not set, defaults to null.
     */
    public function getEnvVar(string $var, $default = null)
    {
        return $this->env[$var] ?? $default;
    }

    public function unsetEnvVar(string $var): void
    {
        unset($this->env[$var]);
    }

    /**
     * @return mixed[]
     */
    public function getEnvVars(): array
    {
        return $this->env;
    }

    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Set an alternate private key used to connect to the repository
     *
     * This method sets:
     * - GIT_SSH environment variable to use the wrapper script included with this library.
     * - GIT_SSH_KEY
     * - GIT_SSH_PORT environment variables
     *
     * @param string|null $wrapperPath Path the the GIT_SSH wrapper script, defaults to null which uses the script
     *  included with this library.
     */
    public function setPrivateKey(string $privateKeyPath, int $port = 22, ?string $wrapperPath = null): void
    {
        if ($wrapperPath === null) {
            $wrapperPath = __DIR__ . '/../bin/git-ssh-wrapper.sh';
        }

        $wrapperPath = realpath($wrapperPath);
        if (! $wrapperPath) {
            throw new GitException(sprintf(
                'Path to GIT_SSH wrapper script "%s" could not be resolved.',
                $wrapperPath
            ));
        }

        $privateKeyPath = realpath($privateKeyPath);
        if (! $privateKeyPath) {
            throw new GitException(sprintf(
                'Path to private key "%s" could not be resolved.',
                $privateKeyPath
            ));
        }

        $this->setEnvVar(self::ENV_GIT_SSH, $wrapperPath);
        $this->setEnvVar(self::ENV_GIT_SSH_KEY, $privateKeyPath);
        $this->setEnvVar(self::ENV_GIT_SSH_PORT, (int) $port);
    }

    /**
     * Unsets the private key by removing the appropriate environment variables.
     */
    public function unsetPrivateKey(): void
    {
        $this->unsetEnvVar(self::ENV_GIT_SSH);
        $this->unsetEnvVar(self::ENV_GIT_SSH_KEY);
        $this->unsetEnvVar(self::ENV_GIT_SSH_PORT);
    }

    /**
     * Returns an object that interacts with a working copy.
     */
    public function workingCopy(string $directory): GitWorkingCopy
    {
        return new GitWorkingCopy($this, $directory);
    }

    public function version(): string
    {
        return $this->git('--version');
    }

    /**
     * @param mixed[] $options
     */
    public function init(string $directory, array $options = []): GitWorkingCopy
    {
        $git = $this->workingCopy($directory);
        $git->init($options);
        $git->setIsCloned(true);
        return $git;
    }

    /**
     * @param mixed[] $options
     */
    public function cloneRepository(string $repository, ?string $directory = null, array $options = []): GitWorkingCopy
    {
        if ($directory === null) {
            $directory = $this->nameParser->parseRepositoryName($repository);
        }

        $git = $this->workingCopy($directory);
        $git->cloneRepository($repository, ...$options);
        $git->setIsCloned(true);

        return $git;
    }

    /**
     * @param string $commandLine Raw command with Git options and arguments. The Git binary should not be in
     * the command, for example `git config -l` would translate to "config -l".
     */
    public function git(string $commandLine, ?string $cwd = null): string
    {
        [$name, $argsAndOptions] = $this->parseCommandLineToNameAndArgsAndOptions($commandLine);

        $command = new GitCommand($name, $argsAndOptions);
        if ($cwd) {
            $command->setDirectory($cwd);
        }

        return $this->run($command);
    }

    public function run(GitCommand $gitCommand, ?string $cwd = null): string
    {
        $process = $this->gitProcessFactory->createFromWrapperCommandAndCwd($this, $gitCommand, $cwd);

        $process->run(function ($type, $buffer) use ($process, $gitCommand): void {
            $outputEvent = new GitOutputEvent($this, $process, $gitCommand, $type, $buffer);
            $this->eventDispatcher->dispatch(GitOutputEvent::class, $outputEvent);
        });

        return $process->getOutput();
    }

    /**
     * @return mixed[]
     */
    private function parseCommandLineToNameAndArgsAndOptions(string $commandLine): array
    {
        $commandLineItems = explode(' ', $commandLine);
        if (! count($commandLineItems)) {
            return ['', []];
        }

        if (Strings::startsWith($commandLineItems[0], '-')) {
            return ['', implode(' ', $commandLineItems)];
        }

        $name = $commandLineItems[0];
        unset($commandLineItems[0]);

        return [$name, implode(' ', $commandLineItems)];
    }
}
