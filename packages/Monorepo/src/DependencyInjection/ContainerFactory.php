<?php declare(strict_types=1);

namespace Symplify\Monorepo\DependencyInjection;

use Psr\Container\ContainerInterface;
use Symplify\Monorepo\HttpKernel\MonorepoKernel;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $monorepoKernel = new MonorepoKernel();
        $monorepoKernel->boot();

        return $monorepoKernel->getContainer();
    }

    public function createWithConfig(string $config): ContainerInterface
    {
        $monorepoKernel = new MonorepoKernel($config);
        $monorepoKernel->boot();

        return $monorepoKernel->getContainer();
    }
}
