<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Process;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

final class ProcessRunner
{
    public function run(string $commandLine): void
    {
        $process = new Process($commandLine);
        $process->run();

        if ($process->isSuccessful()) {
            return;
        }

        throw new ProcessFailedException($process);
    }
}
