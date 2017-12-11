<?php declare(strict_types=1);

namespace Symplify\GitWrapper;

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
     * @var mixed[]
     */
    private $options = [];

    /**
     * Command line arguments passed to the Git command.
     *
     * @var mixed[]
     */
    private $args = [];

    /**
     * Whether command execution should be bypassed.
     *
     * @var bool
     */
    private $bypass = false;

    public function __construct(?string $command = null, ...$argAndOptions)
    {
        if ($command === null) {
            return;
        }

        $this->command = $command;

        if (! count($argAndOptions)) {
            return;
        }

        // If the last element is an array, set it as the options.
        $options = end($argAndOptions);
        if (is_array($options)) {
            $this->setOptions($options);
            array_pop($argAndOptions);
        }

        // Pass all other method arguments as the Git command arguments.
        foreach ($argAndOptions as $arg) {
            $this->addArgument($arg);
        }
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
    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
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
     * @param boolean $bypass Whether to bypass execution of the command. The parameter defaults to true for code readability, however the default behavior of this class is to run the command.
     */
    public function bypass(bool $bypass = true): void
    {
        $this->bypass = (bool) $bypass;
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
        return ! $this->bypass;
    }

    /**
     * Builds the command line options for use in the Git command.
     */
    public function buildOptions(): string
    {
        $options = [];
        foreach ($this->options as $option => $values) {
            foreach ((array) $values as $value) {
                $prefix = (strlen($option) !== 1) ? '--' : '-';
                $rendered = $prefix . $option;
                if ($value !== true) {
                    $rendered .= ($prefix === '--') ? '=' : ' ';
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
    public function setOption(string $option, $value): void
    {
        $this->options[$option] = $value;
    }

    public function setOptions(array $options): void
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    public function setFlag($option): void
    {
        $this->setOption($option, true);
    }

    /**
     * @param string $option The option name, e.g. "branch", "q".
     * @param mixed $default Value that is returned if the option is not set.
     *
     * @return mixed
     */
    public function getOption(string $option, $default = null)
    {
        return $this->options[$option] ?? $default;
    }

    /**
     * @param string $option The option name, e.g. "branch", "q".
     */
    public function unsetOption(string $option): void
    {
        unset($this->options[$option]);
    }

    /**
     * @param string $arg The argument, e.g. the repo URL, directory, etc.
     */
    public function addArgument(string $arg): void
    {
        $this->args[] = $arg;
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
