<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\Statie\HttpKerne\StatieKernel;

// Detect configuration from input
ConfigFileFinder::detectFromInput('statie', new ArgvInput());

// Fallback to file in root
$configFile = ConfigFileFinder::provide('statie', ['statie.yml', 'statie.yaml']);

// Build DI container
$statieKernel = new StatieKernel();
if ($configFile) {
    $statieKernel->bootWithConfig($configFile);
} else {
    $statieKernel->boot();
}

return $statieKernel->getContainer();
