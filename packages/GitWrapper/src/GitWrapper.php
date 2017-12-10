<?php declare(strict_types=1);

namespace Symplify\GitWrapper;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * A wrapper class around the Git binary.
 *
 * A GitWrapper object contains the necessary context to run Git commands such
 * as the path to the Git binary and environment variables. It also provides
 * helper methods to run Git commands as set up the connection to the GIT_SSH
 * wrapper script.
 */
final class GitWrapper
{
    /**
     * Path to the Git binary.
     *
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
     * The timeout of the Git command in seconds, defaults to 60.
     *
     * @var int
     */
    private $timeout = 60;

    /**
     * An array of options passed to the proc_open() function.
     *
     * @var mixed[]
     */
    private $procOptions = [];

    /**
     * @var \GitWrapper\Event\GitOutputListenerInterface
     */
    private $streamListener;

    /**
     * Symfony event dispatcher object used by this library to dispatch events.
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * Constructs a GitWrapper object.
     *
     *   The path to the Git binary. Defaults to null, which uses Symfony's
     *   ExecutableFinder to resolve it automatically.
     *
     *   Throws an exception if the path to the Git binary couldn't be resolved
     *   by the ExecutableFinder class.
     */
    public function __construct(?string $gitBinary = null)
    {
        if ($gitBinary === null) {
            // @codeCoverageIgnoreStart
            $finder = new ExecutableFinder();
            $gitBinary = $finder->find('git');
            if (! $gitBinary) {
                throw new GitException('Unable to find the Git executable.');
            }

            // @codeCoverageIgnoreEnd
        }

        $this->setGitBinary($gitBinary);
    }

    /**
     * Hackish, allows us to use "clone" as a method name.
     *
     * @param mixed[] $args
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $args)
    {
        if ($method === 'clone') {
            return call_user_func_array([$this, 'cloneRepository'], $args);
        }

        $class = get_called_class();
        $message = "Call to undefined method ${class}::${method}()";
        throw new \BadMethodCallException($message);
    }

    /**
     * Gets the dispatcher used by this library to dispatch events.
     */
    public function getDispatcher(): \Symfony\Component\EventDispatcher\EventDispatcherInterface
    {
        if (! isset($this->dispatcher)) {
            $this->dispatcher = new EventDispatcher();
        }

        return $this->dispatcher;
    }

    /**
     * Sets the dispatcher used by this library to dispatch events.
     *
     * The Symfony event dispatcher object.
     */
    public function setDispatcher(EventDispatcherInterface $dispatcher): \GitWrapper\GitWrapper
    {
        $this->dispatcher = $dispatcher;
        return $this;
    }

    /**
     * Sets the path to the Git binary.
     *
     * @param string $gitBinary Path to the Git binary.
     */
    public function setGitBinary(string $gitBinary): \GitWrapper\GitWrapper
    {
        $this->gitBinary = $gitBinary;
        return $this;
    }

    /**
     * Returns the path to the Git binary.
     */
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
    public function setEnvVar(string $var, $value): \GitWrapper\GitWrapper
    {
        $this->env[$var] = $value;
        return $this;
    }

    /**
     * Unsets an environment variable that is defined only in the scope of the
     * Git command.
     *
     * @param string $var The name of the environment variable, e.g. "HOME", "GIT_SSH".
     */
    public function unsetEnvVar(string $var): \GitWrapper\GitWrapper
    {
        unset($this->env[$var]);
        return $this;
    }

    /**
     * Returns an environment variable that is defined only in the scope of the
     * Git command.
     *
     *   The name of the environment variable, e.g. "HOME", "GIT_SSH".
     * @param mixed $default
     *   The value returned if the environment variable is not set, defaults to
     *   null.
     *
     * @return mixed
     */
    public function getEnvVar(string $var, $default = null)
    {
        return isset($this->env[$var]) ? $this->env[$var] : $default;
    }

    /**
     * Returns the associative array of environment variables that are defined
     * only in the scope of the Git command.
     *
     * @return mixed[]
     */
    public function getEnvVars(): array
    {
        return $this->env;
    }

    /**
     * Sets the timeout of the Git command.
     *
     * @param int $timeout The timeout in seconds.
     */
    public function setTimeout(int $timeout): \GitWrapper\GitWrapper
    {
        $this->timeout = (int) $timeout;
        return $this;
    }

    /**
     * Gets the timeout of the Git command.
     *
     *   The timeout in seconds.
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * Sets the options passed to proc_open() when executing the Git command.
     *
     * @param mixed[] $options
     */
    public function setProcOptions(array $options): \GitWrapper\GitWrapper
    {
        $this->procOptions = $options;
        return $this;
    }

    /**
     * Gets the options passed to proc_open() when executing the Git command.
     *
     * @return mixed[]
     */
    public function getProcOptions(): array
    {
        return $this->procOptions;
    }

    /**
     * Set an alternate private key used to connect to the repository.
     *
     * This method sets the GIT_SSH environment variable to use the wrapper
     * script included with this library. It also sets the custom GIT_SSH_KEY
     * and GIT_SSH_PORT environment variables that are used by the script.
     *
     * @param string $privateKey Path to the private key.
     * @param int $port Port that the SSH server being connected to listens on, defaults to 22.
     * @param string|null $wrapper Path the the GIT_SSH wrapper script, defaults to null which uses the
     * script included with this library.
     * @throws GitWrapper\GitException Thrown when any of the paths cannot be resolved.
     */
    public function setPrivateKey(string $privateKey, int $port = 22, ?string $wrapper = null): \GitWrapper\GitWrapper
    {
        if ($wrapper === null) {
            $wrapper = __DIR__ . '/../../bin/git-ssh-wrapper.sh';
        }

        $wrapperPath = realpath($wrapper);
        if (! $wrapperPath) {
            throw new GitException('Path to GIT_SSH wrapper script could not be resolved: ' . $wrapper);
        }

        $privateKeyPath = realpath($privateKey);
        if (! $privateKeyPath) {
            throw new GitException('Path private key could not be resolved: ' . $privateKey);
        }

        return $this
            ->setEnvVar('GIT_SSH', $wrapperPath)
            ->setEnvVar('GIT_SSH_KEY', $privateKeyPath)
            ->setEnvVar('GIT_SSH_PORT', (int) $port);
    }

    /**
     * Unsets the private key by removing the appropriate environment variables.
     */
    public function unsetPrivateKey(): \GitWrapper\GitWrapper
    {
        return $this
            ->unsetEnvVar('GIT_SSH')
            ->unsetEnvVar('GIT_SSH_KEY')
            ->unsetEnvVar('GIT_SSH_PORT');
    }

    /**
     * Adds output listener.
     */
    public function addOutputListener(Event\GitOutputListenerInterface $listener): \GitWrapper\GitWrapper
    {
        $this
            ->getDispatcher()
            ->addListener(Event\GitEvents::GIT_OUTPUT, [$listener, 'handleOutput']);
        return $this;
    }

    /**
     * Adds logger listener listener.
     */
    public function addLoggerListener(Event\GitLoggerListener $listener): self
    {
        $this
            ->getDispatcher()
            ->addSubscriber($listener);
        return $this;
    }

    /**
     * Removes an output listener.
     */
    public function removeOutputListener(Event\GitOutputListenerInterface $listener): \GitWrapper\GitWrapper
    {
        $this
            ->getDispatcher()
            ->removeListener(Event\GitEvents::GIT_OUTPUT, [$listener, 'handleOutput']);
        return $this;
    }

    /**
     * Set whether or not to stream real-time output to STDOUT and STDERR.
     */
    public function streamOutput(bool $streamOutput = true): \GitWrapper\GitWrapper
    {
        if ($streamOutput && ! isset($this->streamListener)) {
            $this->streamListener = new Event\GitOutputStreamListener();
            $this->addOutputListener($this->streamListener);
        }

        if (! $streamOutput && isset($this->streamListener)) {
            $this->removeOutputListener($this->streamListener);
            unset($this->streamListener);
        }

        return $this;
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

    /**
     * Returns the version of the installed Git client.
     */
    public function version(): string
    {
        return $this->git('--version');
    }

    /**
     * Parses name of the repository from the path.
     *
     * For example, passing the "git@github.com:cpliakas/git-wrapper.git"
     * repository would return "git-wrapper".
     *
     * @param string $repository The repository URL.
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
     * Executes a `git init` command.
     *
     * Create an empty git repository or reinitialize an existing one.
     *
     * @param string $directory The directory being initialized.
     * @param mixed[] $options An associative array of command line options.
     * @see GitWorkingCopy::cloneRepository()
     * @ingroup commands
     */
    public function init(string $directory, array $options = []): GitWorkingCopy
    {
        $git = $this->workingCopy($directory);
        $git->init($options);
        $git->setCloned(true);
        return $git;
    }

    /**
     * Executes a `git clone` command and returns a working copy object.
     *
     * Clone a repository into a new directory. Use GitWorkingCopy::clone()
     * instead for more readable code.
     *
     * @param string $repository The Git URL of the repository being cloned.
     * @param string $directory The directory that the repository will be cloned into. If null is
     * passed, the directory will automatically be generated from the URL via
     * the GitWrapper::parseRepositoryName() method.
     * @param mixed[] $options An associative array of command line options.
     * @see GitWorkingCopy::cloneRepository()
     * @ingroup commands
     */
    public function cloneRepository(string $repository, ?string $directory = null, array $options = []): GitWorkingCopy
    {
        if ($directory === null) {
            $directory = self::parseRepositoryName($repository);
        }

        $git = $this->workingCopy($directory);
        $git->clone($repository, $options);
        $git->setCloned(true);
        return $git;
    }

    /**
     * Runs an arbitrary Git command.
     *
     * The command is simply a raw command line entry for everything after the
     * Git binary. For example, a `git config -l` command would be passed as
     * `config -l` via the first argument of this method.
     *
     * Note that no events are thrown by this method.
     *
     *   The raw command containing the Git options and arguments. The Git
     *   binary should not be in the command, for example `git config -l` would
     *   translate to "config -l".
     *   The working directory of the Git process. Defaults to null which uses
     *   the current working directory of the PHP process.
     *
     *   The STDOUT returned by the Git command.
     *
     * @see GitWrapper::run()
     */
    public function git(string $commandLine, ?string $cwd = null): string
    {
        $command = GitCommand::getInstance($commandLine);
        $command->setDirectory($cwd);
        return $this->run($command);
    }

    /**
     * Runs a Git command.
     *
     *   The Git command being executed.
     *   Explicitly specify the working directory of the Git process. Defaults
     *   to null which automatically sets the working directory based on the
     *   command being executed relative to the working copy.
     *
     *   The STDOUT returned by the Git command.
     *
     * @see Process
     */
    public function run(GitCommand $command, ?string $cwd = null): string
    {
        $wrapper = $this;
        $process = new GitProcess($this, $command, $cwd);
        $process->run(function ($type, $buffer) use ($wrapper, $process, $command): void {
            $event = new Event\GitOutputEvent($wrapper, $process, $command, $type, $buffer);
            $wrapper->getDispatcher()->dispatch(Event\GitEvents::GIT_OUTPUT, $event);
        });
        return $command->notBypassed() ? $process->getOutput() : '';
    }
}
