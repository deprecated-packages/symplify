<?php

declare(strict_types=1);

use Symplify\PHPUnitUpgrader\HttpKernel\PHPUnitUpgraderKernel;
use Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;

$possibleAutoloadPaths = [
    // after split package
    __DIR__ . '/../vendor/autoload.php',
    // dependency
    __DIR__ . '/../../../autoload.php',
    // monorepo
    __DIR__ . '/../../../vendor/autoload.php',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;
        break;
    }
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(PHPUnitUpgraderKernel::class);
$kernelBootAndApplicationRun->run();
