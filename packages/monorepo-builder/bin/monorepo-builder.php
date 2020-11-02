<?php

// decoupled in own "*.php" file, so ECS, Rector and PHPStan works out of the box here

declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\MonorepoBuilder\Console\MonorepoBuilderApplication;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\MonorepoBuilder\ValueObject\File;
use Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use Symplify\SetConfigResolver\ConfigResolver;

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

// the environment name must be "random", so configs are invalidated without clearing the cache
$environment = 'prod' . random_int(0, 100000);
$monorepoBuilderKernel = new MonorepoBuilderKernel($environment, StaticInputDetector::isDebug());
if ($configFileInfos !== []) {
    $monorepoBuilderKernel->setConfigs($configFileInfos);
}
$monorepoBuilderKernel->boot();

$container = $monorepoBuilderKernel->getContainer();

# 3. run it
$application = $container->get(MonorepoBuilderApplication::class);
exit($application->run());
