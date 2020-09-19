<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Process;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

final class ProcessRunner
{
    /**
     * @param string[] $command
     */
    public function createAndRun(array $command, string $cwd, OutputInterface $output): void
    {
        $process = new Process($command, $cwd, null, null, null);
        $process->mustRun(static function (string $type, string $buffer) use ($output): void {
            $output->write($buffer);
        });
    }
}
