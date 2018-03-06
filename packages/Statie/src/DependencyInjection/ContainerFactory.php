<?php declare(strict_types=1);

namespace Symplify\Statie\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;

final class ContainerFactory
{
    public function create(): Container
    {
        $appKernel = new StatieKernel();
        $appKernel->boot();

        return $appKernel->getContainer();
    }

    public function createWithConfig(string $config): Container
    {
        $appKernel = new StatieKernel($config);
        $appKernel->boot();

        return $appKernel->getContainer();
    }
}
