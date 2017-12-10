<?php declare(strict_types=1);

namespace GitWrapper;

/**
 * Base class extended by all Git command classes.
 */
final class GitCommand
{
    /**
     * Path to the directory containing the working copy. If this variable is
     * set, then the process will change into this directory while the Git
     * command is being run.
     *
     * @var string|null
     */
    private $directory;

    /**
     * The command being run, e.g. "clone", "commit", etc.
     *
     * @var string
     */
    private $command = '';

    /**
     * An associative array of command line options and flags.
     *
     * @var array
     */
    private $options = [];

    /**
     * Command line arguments passed to the Git command.
     *
     * @var array
     */
    private $args = [];

    /**
     * Whether command execution should be bypassed.
     *
     * @var boolean
     */
    private $bypass = false;

    /**
     * Constructs a GitCommand object.
     *
     * Use GitCommand::getInstance() as the factory method for this class.
     *
     * @param array $args The arguments passed to GitCommand::getInstance().
     */
    private function __construct(array $args)
    {
        if ($args) {
            // The first argument is the command.
            $this->command = array_shift($args);

            // If the last element is an array, set it as the options.
            $options = end($args);
            if (is_array($options)) {
                $this->setOptions($options);
                array_pop($args);
            }

            // Pass all other method arguments as the Git command arguments.
            foreach ($args as $arg) {
                $this->addArgument($arg);
            }
        }
    }

    /**
     * Constructs a GitCommand object.
     *
     * Accepts a variable number of arguments to model the arguments passed to
     * the Git command line utility. If the last argument is an array, it is
     * passed as the command options.
     *
     * @param string $command The Git command being run, e.g. "clone", "commit", etc.
     * @param string ...$arguments Zero or more arguments passed to the Git command.
     * @param array $options An optional array of arguments to pass to the command.
     */
    public static function getInstance(): \GitWrapper\GitCommand
    {
        $args = func_get_args();
        return new static($args);
    }

    /**
     * Returns Git command being run, e.g. "clone", "commit", etc.
     */
    public function getCommand(): string
    {
        return $this->command;
    }

    /**
     * Sets the path to the directory containing the working copy.
     *
     * @param string $directory The path to the directory containing the working copy.
     */
    public function setDirectory(string $directory): \GitWrapper\GitCommand
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * Gets the path to the directory containing the working copy.
     *
     * @return string|null The path, null if no path is set.
     */
    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    /**
     * A boolean flagging whether to skip running the command.
     *
     * @param boolean $bypass Whether to bypass execution of the command. The parameter defaults to
     * true for code readability, however the default behavior of this class is to run the command.
     */
    public function bypass(bool $bypass = true): \GitWrapper\GitCommand
    {
        $this->bypass = (bool) $bypass;
        return $this;
    }

    /**
     * Returns true if the Git command should be run.
     *
     * The return value is the boolean opposite $this->bypass. Although this
     * seems complex, it makes the code more readable when checking whether the
     * command should be run or not.
     *
     * @return boolean If true, the command should be run.
     */
    public function notBypassed(): bool
    {
        return !$this->bypass;
    }

    /**
     * Builds the command line options for use in the Git command.
     */
    public function buildOptions(): string
    {
        $options = [];
        foreach ($this->options as $option => $values) {
            foreach ((array) $values as $value) {
                $prefix = (strlen($option) != 1) ? '--' : '-';
                $rendered = $prefix . $option;
                if ($value !== true) {
                    $rendered .= ('--' == $prefix) ? '=' : ' ';
                    $rendered .= $value;
                }

                $options[] = $rendered;
            }
        }

        return implode(' ', $options);
    }

    /**
     * Sets a command line option.
     *
     * Option names are passed as-is to the command line, whereas the values are
     * escaped using \Symfony\Component\Process\ProcessUtils.
     *
     * @param string $option The option name, e.g. "branch", "q".
     * @param string|true $value The option's value, pass true if the options is a flag.
     */
    public function setOption(string $option, $value): GitCommand
    {
        $this->options[$option] = $value;
        return $this;
    }

    /**
     * Sets multiple command line options.
     *
     * @param array $options An associative array of command line options.
     *
     * @return \GitWrapper\GitCommand
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }

        return $this;
    }

    /**
     * Sets a command line flag.
     *
     * @param string $flag The flag name, e.g. "q", "a".
     *
     * @see \GitWrapper\GitCommand::setOption()
     */
    public function setFlag($option): GitCommand
    {
        return $this->setOption($option, true);
    }

    /**
     * Gets a command line option.
     *
     * @param string $option The option name, e.g. "branch", "q".
     * @param mixed $default Value that is returned if the option is not set, defaults to null.
     *
     * @return mixed
     */
    public function getOption(string $option, $default = null)
    {
        return (isset($this->options[$option])) ? $this->options[$option] : $default;
    }

    /**
     * Unsets a command line option.
     *
     * @param string $option The option name, e.g. "branch", "q".
     */
    public function unsetOption(string $option): \GitWrapper\GitCommand
    {
        unset($this->options[$option]);
        return $this;
    }

    /**
     * Adds a command line argument passed to the Git command.
     *
     * @param string $arg The argument, e.g. the repo URL, directory, etc.
     */
    public function addArgument(string $arg): \GitWrapper\GitCommand
    {
        $this->args[] = $arg;
        return $this;
    }

    /**
     * Renders the arguments and options for the Git command.
     *
     * @see GitCommand::getCommand()
     * @see GitCommand::buildOptions()
     */
    public function getCommandLine(): string
    {
        $command = [
            $this->getCommand(),
            $this->buildOptions(),
            implode(' ', $this->args),
        ];
        return implode(' ', array_filter($command));
    }
}
