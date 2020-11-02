<?php

// decoupled in own "*.php" file, so ECS, Rector and PHPStan works out of the box here

declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\ChangelogLinker\Console\ChangelogApplication;
use Symplify\ChangelogLinker\HttpKernel\ChangelogLinkerKernel;
use Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use Symplify\SetConfigResolver\ConfigResolver;

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
$inputConfigFileInfos = $configResolver->resolveFromInputWithFallback(new ArgvInput(), ['changelog-linker.php']);

if ($inputConfigFileInfos !== null) {
    $configFileInfos[] = $inputConfigFileInfos;
}

// create container
// random has is needed, so cache is invalidated and changes from config are loaded
$environment = 'prod' . random_int(1, 100000);
$changelogLinkerKernel = new ChangelogLinkerKernel($environment, StaticInputDetector::isDebug());
if ($configFileInfos) {
    $changelogLinkerKernel->setConfigs($configFileInfos);
}

$changelogLinkerKernel->boot();

$container = $changelogLinkerKernel->getContainer();

// run application
$application = $container->get(ChangelogApplication::class);
exit($application->run());
