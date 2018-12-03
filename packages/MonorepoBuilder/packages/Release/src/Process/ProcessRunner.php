<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Process;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class ProcessRunner
{
    /**
     * Reasonable timeout to report hang off: 10 minutes
     * @var float
     */
    private const TIMEOUT = 10 * 60.0;

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @param string|string[] $commandLine
     */
    public function run($commandLine, bool $shouldDisplayOutput = false): string
    {
        if ($this->symfonyStyle->isVerbose()) {
            $this->symfonyStyle->note('Running process: ' . $this->normalizeToString($commandLine));
        }

        $process = $this->createProcess($commandLine);
        $process->run();

        $this->reportResult($shouldDisplayOutput, $process);

        return $process->getOutput();
    }

    /**
     * @param string|string[] $content
     */
    private function normalizeToString($content): string
    {
        if (is_array($content)) {
            return implode(' ', $content);
        }

        return $content;
    }

    /**
     * @param string|string[] $commandLine
     */
    private function createProcess($commandLine): Process
    {
        // @since Symfony 4.2: https://github.com/symfony/symfony/pull/27821
        if (is_string($commandLine) && method_exists(Process::class, 'fromShellCommandline')) {
            return Process::fromShellCommandline($commandLine, null, null, null, self::TIMEOUT);
        }

        return new Process($commandLine, null, null, null, self::TIMEOUT);
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
