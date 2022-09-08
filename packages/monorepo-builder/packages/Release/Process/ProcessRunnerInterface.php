<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\Process;

interface ProcessRunnerInterface
{
    /**
     * @param string|string[] $commandLine
     */
    public function run(string|array $commandLine, ?string $cwd = null): string;
}
