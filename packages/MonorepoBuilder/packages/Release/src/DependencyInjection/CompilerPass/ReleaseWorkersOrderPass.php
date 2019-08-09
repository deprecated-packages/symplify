<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\Release\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symplify\MonorepoBuilder\Release\ReleaseWorkerProvider;

class ReleaseWorkersOrderPass implements CompilerPassInterface
{
    const WORKERS_PARAMETER_NAME = 'release_workers';

    public function process(ContainerBuilder $containerBuilder): void
    {
        $provider = $containerBuilder->getDefinition(ReleaseWorkerProvider::class);
        $serviceNames = $containerBuilder->getParameter(self::WORKERS_PARAMETER_NAME);
        foreach ($serviceNames as $serviceName) {
            $provider->addMethodCall('addReleaseWorker', [new Reference($serviceName)]);
        }
    }
}
