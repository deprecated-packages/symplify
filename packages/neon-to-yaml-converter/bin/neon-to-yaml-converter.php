<?php

declare(strict_types=1);

use Symplify\NeonToYamlConverter\HttpKernel\NeonToYamlKernel;
use Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;

# 1. autoload
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

# 2. create container
$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(NeonToYamlKernel::class);
$kernelBootAndApplicationRun->run();
