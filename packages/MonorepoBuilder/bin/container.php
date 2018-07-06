<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\MonorepoBuilder\DependencyInjection\ContainerFactory;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;

$config = null;

ConfigFileFinder::detectFromInput('mb', new ArgvInput());
$configFile = ConfigFileFinder::provide('mb', ['monorepo-builder.yml', 'monorepo-builder.yaml']);

$containerFactory = new ContainerFactory();
if ($configFile) {
    return $containerFactory->createWithConfig($configFile);
}

return $containerFactory->create();
