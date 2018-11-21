<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Process;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class ProcessRunner
{
    public function run(string $commandLine): string
    {
        $process = new Process($commandLine);
        $process->run();

        if ($process->isSuccessful()) {
            return trim($process->getOutput());
        }

        throw new ProcessFailedException($process);
    }
}
