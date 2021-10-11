<?php

declare(strict_types=1);

use Symplify\EasyCI\HttpKernel\EasyCIKernel;
use Symplify\SymplifyKernel\ValueObject\KernelBootAndApplicationRun;

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

$easyCIFilePath = getcwd() . '/easy-ci.php';
if (file_exists($easyCIFilePath)) {
    $extraConfigs[] = new \Symplify\SmartFileSystem\SmartFileInfo($easyCIFilePath);
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(EasyCIKernel::class, $extraConfigs);
$kernelBootAndApplicationRun->run();
