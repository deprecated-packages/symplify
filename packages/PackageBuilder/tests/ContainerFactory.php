<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests;

use Symfony\Component\DependencyInjection\Container;

final class ContainerFactory
{
    public function createWithConfig(string $configPath): Container
    {
        $appKernel = new AppKernel($configPath);
        $appKernel->boot();

        return $appKernel->getContainer();
    }
}
