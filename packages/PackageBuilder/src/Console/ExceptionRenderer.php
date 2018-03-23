<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Console;

use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class ExceptionRenderer
{
    /**
     * @var string[]
     */
    private $verbosityOptionToLevel = [
        '-v' => OutputInterface::VERBOSITY_VERBOSE,
        '-vv' => OutputInterface::VERBOSITY_VERY_VERBOSE,
        '-vvv' => OutputInterface::VERBOSITY_DEBUG
    ];

    /**
     * @var Application
     */
    private $application;

    /**
     * @var ConsoleOutput
     */
    private $consoleOutput;

    public function __construct()
    {
        $this->application = new Application();
        $this->consoleOutput = $this->createConsoleOutput();
    }

    public function render(Exception $exception): void
    {
        $this->application->renderException($exception, $this->consoleOutput);
    }

    private function createConsoleOutput(): ConsoleOutput
    {
        $consoleOutput = new ConsoleOutput();
        foreach ($this->verbosityOptionToLevel as $option => $level) {
            if (in_array($option, $_SERVER['argv'], true)) {
                $consoleOutput->setVerbosity($level);
            }
        }

        return $consoleOutput;
    }
}
