<?php

declare(strict_types=1);

use Migrify\MigrifyKernel\Bootstrap\KernelBootAndApplicationRun;
use Symplify\VendorPatches\HttpKernel\VendorPatchesKernel;

$possibleAutoloadPaths = [
    __DIR__ . '/../autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../../../vendor/autoload.php',
];

foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (! file_exists($possibleAutoloadPath)) {
        continue;
    }

    require_once $possibleAutoloadPath;
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(VendorPatchesKernel::class);
$kernelBootAndApplicationRun->run();
