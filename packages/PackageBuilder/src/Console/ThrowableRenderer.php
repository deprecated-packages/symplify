<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Console;

use Error;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

final class ThrowableRenderer
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var int[]
     */
    private $verbosityOptionToLevel = [
        '-v' => OutputInterface::VERBOSITY_VERBOSE,
        '-vv' => OutputInterface::VERBOSITY_VERY_VERBOSE,
        '-vvv' => OutputInterface::VERBOSITY_DEBUG,
    ];

    public function __construct(?OutputInterface $output = null, ?InputInterface $input = null)
    {
        $this->application = new Application();
        $this->output = $output ?: new ConsoleOutput();
        $this->input = $input ?: new ArgvInput();

        $this->decorateOutput($this->output);
    }

    /**
     * @inspiration https://github.com/symfony/symfony/pull/25255/files
     */
    public function render(Throwable $throwable): void
    {
        if ($throwable instanceof Error) {
            $throwable = new FatalThrowableError($throwable);
        }

        $this->application->renderException($throwable, $this->output);
    }

    private function decorateOutput(OutputInterface $output): void
    {
        foreach ($this->verbosityOptionToLevel as $option => $level) {
            if ($this->input->hasParameterOption($option)) {
                $output->setVerbosity($level);
            }
        }
    }
}
