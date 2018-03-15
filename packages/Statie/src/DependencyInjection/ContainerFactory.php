<?php declare(strict_types=1);

namespace Symplify\Statie\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerInterface;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $appKernel = new StatieKernel();
        $appKernel->boot();

        return $appKernel->getContainer();
    }

    public function createWithConfig(string $config): ContainerInterface
    {
        $appKernel = new StatieKernel();
        $appKernel->bootWithConfig($config);

        return $appKernel->getContainer();
    }
}
