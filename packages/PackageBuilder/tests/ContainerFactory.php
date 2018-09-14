<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests;

use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerFactory
{
    public function createWithConfig(string $configPath): ContainerInterface
    {
        $appKernel = new PackageBuilderTestKernel($configPath);
        $appKernel->boot();

        return $appKernel->getContainer();
    }
}
