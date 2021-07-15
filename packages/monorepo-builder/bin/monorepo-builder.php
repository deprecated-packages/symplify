<?php

// decoupled in own "*.php" file, so ECS, Rector and PHPStan works out of the box here

declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;

use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\ValueObject\File;
use Symplify\SmartFileSystem\SmartFileInfo;
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

$scoperAutoloadFilepath = __DIR__ . '/../vendor/scoper-autoload.php';
if (file_exists($scoperAutoloadFilepath)) {
    require_once $scoperAutoloadFilepath;
}


$configFileInfos = [];

$argvInput = new ArgvInput();
$configFileInfo = resolveConfigFileInfo($argvInput);
if ($configFileInfo instanceof SmartFileInfo) {
    $configFileInfos[] = $configFileInfo;
}

$kernelBootAndApplicationRun = new KernelBootAndApplicationRun(MonorepoBuilderKernel::class, $configFileInfos);
$kernelBootAndApplicationRun->run();



function resolveConfigFileInfo(ArgvInput $argvInput): ?SmartFileInfo
{
    if ($argvInput->hasParameterOption(['-c', '--config'])) {
        $configOption = $argvInput->getParameterOption(['-c', '--config']);
        if (is_string($configOption) && file_exists($configOption)) {
            return new SmartFileInfo($configOption);
        }
    }

    $defaultConfigFilePath = getcwd() . '/' . File::CONFIG;
    if (file_exists($defaultConfigFilePath)) {
        return new SmartFileInfo($defaultConfigFilePath);
    }

    return null;
}
