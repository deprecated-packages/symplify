<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Process;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class ProcessRunner
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    public function run(string $commandLine, bool $shouldDisplayOutput = false): void
    {
        if ($this->symfonyStyle->isVerbose()) {
            $this->symfonyStyle->note('Running process: ' . $commandLine);
        }

        // set reasonable timeout to report hang off, 10 minutes
        $process = new Process($commandLine, null, null, null, 10 * 60.0);
        $process->run();

        $this->reportResult($shouldDisplayOutput, $process);
    }

    private function reportResult(bool $shouldDisplayOutput, Process $process): void
    {
        if ($process->isSuccessful()) {
            if ($shouldDisplayOutput) {
                $this->symfonyStyle->writeln(trim($process->getOutput()));
            }

            return;
        }

        throw new ProcessFailedException($process);
    }
}
