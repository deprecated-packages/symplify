<?php

// decoupled in own "*.php" file, so ECS, Rector and PHPStan works out of the box here

declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\ValueObject\File;
use Symplify\SetConfigResolver\ConfigResolver;
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


$configFileInfos = [];

$configResolver = new ConfigResolver();
$inputConfigFileInfo = $configResolver->resolveFromInputWithFallback(new ArgvInput(), [File::CONFIG]);

if ($inputConfigFileInfo !== null) {
    $configFileInfos[] = $inputConfigFileInfo;
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(MonorepoBuilderKernel::class, $configFileInfos);
$kernelBootAndApplicationRun->run();
