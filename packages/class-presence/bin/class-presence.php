<?php

declare(strict_types=1);

use Symplify\ClassPresence\HttpKernel\ClassPresenceKernel;
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

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(ClassPresenceKernel::class);
$kernelBootAndApplicationRun->run();
