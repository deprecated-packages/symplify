<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\Statie\DependencyInjection\ContainerFactory;

require_once __DIR__ . '/statie-autoload.php';

// 1. Detect configuration from input
ConfigFileFinder::detectFromInput('statie', new ArgvInput());

// 2. Fallback to file in root
$configFile = ConfigFileFinder::provide('statie', ['statie.yml', 'statie.yaml']);

// 3. Build DI container
$containerFactory = new ContainerFactory();
if ($configFile) {
    return $containerFactory->createWithConfig($configFile);
}

return $containerFactory->create();
