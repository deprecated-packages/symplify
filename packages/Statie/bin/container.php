<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\Statie\HttpKernel\StatieKernel;

// Detect configuration from input
ConfigFileFinder::detectFromInput('statie', new ArgvInput());

// Fallback to file in root
$configFile = ConfigFileFinder::provide('statie', ['statie.yml', 'statie.yaml']);

function isDebug(): bool
{
    $argvInput = new ArgvInput();
    return (bool) $argvInput->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
}

$statieKernel = new StatieKernel('prod', isDebug());
if ($configFile) {
    $statieKernel->setConfigs([$configFile]);
}
$statieKernel->boot();

return $statieKernel->getContainer();
