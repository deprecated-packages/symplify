<?php

declare(strict_types=1);

namespace Symplify\Statie\Migrator\Contract;

interface MigratorWorkerInterface
{
    public function processSourceDirectory(string $sourceDirectory, string $workingDirectory): void;
}
