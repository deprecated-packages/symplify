<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\PackageBuilder\Console\Input\InputDetector;
use Symplify\Statie\HttpKernel\StatieKernel;

// Detect configuration from input
ConfigFileFinder::detectFromInput('statie', new ArgvInput());

// Fallback to file in root
$configFile = ConfigFileFinder::provide('statie', ['statie.yml', 'statie.yaml']);

$statieKernel = new StatieKernel('prod', InputDetector::isDebug());
if ($configFile !== null) {
    $statieKernel->setConfigs([$configFile]);
}
$statieKernel->boot();

return $statieKernel->getContainer();
