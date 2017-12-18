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
     * @var string
     */
    private $command;

    /**
     * @var mixed[]
     */
    private $options = [];

    /**
     * @var mixed[]
     */
    private $args = [];

    public function __construct(string $command = '', ...$argsAndOptions)
    {
        $this->command = $command;

        foreach ($argsAndOptions as $argOrOption) {
            if (is_array($argOrOption)) {
                // If item is array, set it as the options
                $this->setOptions($argOrOption);
            } else {
                // Pass all other as the Git command arguments
                $this->addArgument($argOrOption);
            }
        }
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function setDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    /**
     * @return mixed[]
     */
    public function buildOptions(): array
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

        return $options;
    }

    /**
     * Option names are passed as-is to the command line, whereas the values are escaped.
     *
     * @param string|true $value The option's value, pass true if the options is a flag.
     */
    public function setOption(string $option, $value): void
    {
        $this->options[$option] = $value;
    }

    /**
     * @param mixed[] $options
     */
    public function setOptions(array $options): void
    {
        foreach ($options as $option => $value) {
            $this->setOption($option, $value);
        }
    }

    /**
     * @param mixed $default Value that is returned if the option is not set.
     */
    public function getOption(string $option, $default = null)
    {
        return $this->options[$option] ?? $default;
    }

    public function unsetOption(string $option): void
    {
        unset($this->options[$option]);
    }

    public function addArgument(string $arg): void
    {
        $this->args[] = $arg;
    }

    /**
     * Provide CLI items for Process 1st arguments constructor
     *
     * @return mixed[]
     */
    public function getCommandLineItems(): array
    {
        $items = array_merge(
            [$this->command],
            $this->buildOptions(),
            $this->args
        );

        // remove empty values
        return array_filter($items);
    }
}
