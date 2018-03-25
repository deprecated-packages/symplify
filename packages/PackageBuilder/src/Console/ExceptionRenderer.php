<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Console;

use Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class ExceptionRenderer
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
     * @var ArgvInput
     */
    private $input;

    public function __construct(?OutputInterface $output = null, ?InputInterface $input = null)
    {
        $this->application = new Application();
        $this->output = $output ?: new ConsoleOutput();
        $this->input = $input ?: new ArgvInput();

        $this->decorateOutput($this->output);
    }

    public function render(Exception $exception): void
    {
        $this->application->renderException($exception, $this->output);
    }

    private function decorateOutput(OutputInterface $output): void
    {
        if ($this->input->hasParameterOption('v')) {
            $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
        }
    }
}
