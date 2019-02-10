<?php declare(strict_types=1);

use Symfony\Component\Console\Input\ArgvInput;
use Symplify\MonorepoBuilder\HttpKernel\MonorepoBuilderKernel;
use Symplify\PackageBuilder\Configuration\ConfigFileFinder;
use Symplify\PackageBuilder\Console\Input\InputDetector;

ConfigFileFinder::detectFromInput('mb', new ArgvInput());
$configFile = ConfigFileFinder::provide('mb', ['monorepo-builder.yml', 'monorepo-builder.yaml']);

$monorepoBuilderKernel = new MonorepoBuilderKernel('prod', InputDetector::isDebug());

if ($configFile) {
    $monorepoBuilderKernel->setConfigs([$configFile]);
}

$monorepoBuilderKernel->boot();

return $monorepoBuilderKernel->getContainer();
