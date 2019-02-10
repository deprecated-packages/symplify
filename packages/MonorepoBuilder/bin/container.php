<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;

ConfigFileFinder::detectFromInput('mb', new ArgvInput());
$configFile = ConfigFileFinder::provide('mb', ['monorepo-builder.yml', 'monorepo-builder.yaml']);

$isDebug = (bool) (new ArgvInput())->hasParameterOption(['--debug', '-v', '-vv', '-vvv']);
$monorepoBuilderKernel = new MonorepoBuilderKernel('prod', $isDebug);

if ($configFile) {
    $monorepoBuilderKernel->setConfigs([$configFile]);
}

$monorepoBuilderKernel->boot();

return $monorepoBuilderKernel->getContainer();
