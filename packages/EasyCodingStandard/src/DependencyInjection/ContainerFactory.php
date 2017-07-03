<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $appKernel = new AppKernel;
        $appKernel->boot();

        return $appKernel->getContainer();
    }

    /**
     * @param string[] $configs
     */
    public function createWithConfigs(array $configs): ContainerInterface
    {
        $appKernel = new AppKernel($configs);
        $appKernel->boot();

        return $appKernel->getContainer();
    }
}
