<?php

declare(strict_types=1);

use Symplify\StaticDetector\HttpKernel\StaticDetectorKernel;
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

$extraConfigs = [];
$extraConfig = getcwd() . '/static-detector.php';
if (file_exists($extraConfig)) {
    $extraConfigs[] = $extraConfig;
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(StaticDetectorKernel::class, $extraConfigs);
$kernelBootAndApplicationRun->run();
