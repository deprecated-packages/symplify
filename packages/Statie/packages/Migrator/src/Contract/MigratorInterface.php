<?php

declare(strict_types=1);

namespace Symplify\Statie\Migrator\Contract;

interface MigratorInterface
{
    public function migrate(string $workingDirectory): void;
}
