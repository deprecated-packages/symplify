<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests;

use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    public function createWithConfig(string $configPath): ContainerInterface
    {
        $appKernel = new AppKernel($configPath);
        $appKernel->boot();

        return $appKernel->getContainer();
    }
}
