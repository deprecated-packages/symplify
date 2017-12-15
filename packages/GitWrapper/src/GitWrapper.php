<?php declare(strict_types=1);

namespace Symplify\GitWrapper;

use Nette\Utils\Strings;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symplify\GitWrapper\Contract\EventListener\GitOutputListenerInterface;
use Symplify\GitWrapper\Event\GitEvents;
use Symplify\GitWrapper\Event\GitOutputEvent;
use Symplify\GitWrapper\EventListener\GitLoggerListener;
use Symplify\GitWrapper\EventListener\GitOutputStreamListener;
use Symplify\GitWrapper\Exception\GitException;
use Symplify\GitWrapper\Process\GitProcess;

/**
 * A wrapper class around the Git binary.
 *
 * A GitWrapper object contains the necessary context to run Git commands such  as the path to the Git binary
 * and environment variables.
 *
 * It also provides helper methods to run Git commands as set up the connection to the GIT_SSH
 * wrapper script.
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
     * @var GitOutputListenerInterface
     */
    private $gitOutputListener;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param string $gitBinary The path to the Git binary. Defaults to null, which uses Symfony's
     * ExecutableFinder to resolve it automatically.
     */
    public function __construct(?string $gitBinary = null)
    {
        if ($gitBinary === null) {
            $finder = new ExecutableFinder();
            $gitBinary = $finder->find('git');
            if (! $gitBinary) {
                throw new GitException('Unable to find the Git executable.');
            }
        }

        $this->gitBinary = $gitBinary;
    }

    /**
     * Gets the dispatcher used by this library to dispatch events.
     *
     * @todo ctor?
     */
    public function getDispatcher(): EventDispatcherInterface
    {
        if ($this->eventDispatcher === null) {
            $this->eventDispatcher = new EventDispatcher();
        }

        return $this->eventDispatcher;
    }

    /**
     * @todo use EventDispatcher via constructor
     */
    public function setDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function setGitBinary(string $gitBinary): void
    {
        $this->gitBinary = $gitBinary;
    }

    public function getGitBinary(): string
    {
        return $this->gitBinary;
    }

    /**
     * Sets an environment variable that is defined only in the scope of the Git
     * command.
     *
     * @param string $var The name of the environment variable, e.g. "HOME", "GIT_SSH".
     * @param mixed $value
     */
    public function setEnvVar(string $var, $value): void
    {
        $this->env[$var] = $value;
    }

    /**
     * Unsets an environment variable that is defined only in the scope of the Git command.
     *
     * @param string $var The name of the environment variable, e.g. "HOME", "GIT_SSH".
     */
    public function unsetEnvVar(string $var): void
    {
        unset($this->env[$var]);
    }

    /**
     * Returns an environment variable that is defined only in the scope of the
     * Git command.
     *
     * @param string $var The name of the environment variable, e.g. "HOME", "GIT_SSH".
     * @param mixed $default The value returned if the environment variable is not set, defaults to null.
     *
     * @return mixed
     */
    public function getEnvVar(string $var, $default = null)
    {
        return $this->env[$var] ?? $default;
    }

    /**
     * Returns the associative array of environment variables that are defined only in the scope of the Git command.
     *
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
     * @todo resolve via DI and factory
     */
    public function addOutputListener(GitOutputListenerInterface $gitOutputListener): void
    {
        $this->getDispatcher()
            ->addListener(GitEvents::GIT_OUTPUT, [$gitOutputListener, 'handleOutput']);
    }

    /**
     * @todo resolve via DI and factory
     */
    public function addLoggerListener(GitLoggerListener $gitLoggerListener): void
    {
        $this->getDispatcher()
            ->addSubscriber($gitLoggerListener);
    }

    /**
     * @todo resolve via DI and factory
     */
    public function removeOutputListener(GitOutputListenerInterface $gitOutputListener): void
    {
        $this->getDispatcher()
            ->removeListener(GitEvents::GIT_OUTPUT, [$gitOutputListener, 'handleOutput']);
    }

    /**
     * Set whether or not to stream real-time output to STDOUT and STDERR.
     */
    public function streamOutput(bool $streamOutput = true): void
    {
        if ($streamOutput && $this->gitOutputListener === null) {
            $this->gitOutputListener = new GitOutputStreamListener();
            $this->addOutputListener($this->gitOutputListener);
        }

        if (! $streamOutput && $this->gitOutputListener !== null) {
            $this->removeOutputListener($this->gitOutputListener);
            unset($this->gitOutputListener);
        }
    }

    /**
     * Returns an object that interacts with a working copy.
     *
     * @param string $directory Path to the directory containing the working copy.
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
     * @todo decouple to external service
     *
     * Parses name of the repository from the path.
     *
     * E.g. passing the "git@github.com:cpliakas/git-wrapper.git"
     * repository would return "git-wrapper".
     */
    public static function parseRepositoryName(string $repository): string
    {
        $scheme = parse_url($repository, PHP_URL_SCHEME);

        if ($scheme === null) {
            $parts = explode('/', $repository);
            $path = end($parts);
        } else {
            $strpos = strpos($repository, ':');
            $path = substr($repository, $strpos + 1);
        }

        return basename($path, '.git');
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
            $directory = self::parseRepositoryName($repository);
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
        $process = new GitProcess($this, $gitCommand, $cwd);

        $process->run(function ($type, $buffer) use ($process, $gitCommand): void {
            $event = new GitOutputEvent($this, $process, $gitCommand, $type, $buffer);
            $this->getDispatcher()
                ->dispatch(GitEvents::GIT_OUTPUT, $event);
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
