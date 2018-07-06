<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\ChangelogLinker\DependencyInjection\ContainerFactory;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;

$config = null;

ConfigFileFinder::detectFromInput('cl', new ArgvInput());
$configFile = ConfigFileFinder::provide('cl', ['changelog-linker.yml', 'changelog-linker.yaml']);

$containerFactory = new ContainerFactory();
if ($configFile) {
    return $containerFactory->createWithConfig($configFile);
}

return $containerFactory->create();
