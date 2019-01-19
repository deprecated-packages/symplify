<?php declare(strict_types=1);

namespace Symplify\Statie\MigratorJekyll\Contract;

interface MigratorJekyllWorkerInterface
{
    public function processSourceDirectory(string $sourceDirectory, string $workingDirectory): void;
}
