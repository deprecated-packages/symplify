<?php

declare(strict_types=1);

use Migrify\MigrifyKernel\Bootstrap\KernelBootAndApplicationRun;
use Symplify\EasyCI\HttpKernel\EasyCIKernel;

$possibleAutoloadPaths = [
    // dependency
    __DIR__ . '/../../../autoload.php',
    // after split package
    __DIR__ . '/../vendor/autoload.php',
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
$extraConfig = getcwd() . '/easy-ci.php';
if (file_exists($extraConfig)) {
    $extraConfigs[] = $extraConfig;
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(EasyCIKernel::class, $extraConfigs);
$kernelBootAndApplicationRun->run();
