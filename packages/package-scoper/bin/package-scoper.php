<?php

declare(strict_types=1);

// decoupled in own "*.php" file, so ECS, Rector and PHPStan works out of the box here

use Symplify\PackageScoper\HttpKernel\PackageScoperKernel;
use Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;

# autoload
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


$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(PackageScoperKernel::class);
$kernelBootAndApplicationRun->run();
