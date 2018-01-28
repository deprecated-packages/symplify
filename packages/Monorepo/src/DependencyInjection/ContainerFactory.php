<?php declare(strict_types=1);

namespace Symplify\Monorepo\DependencyInjection;

use Psr\Container\ContainerInterface;
use Symplify\Monorepo\MonorepoKernel;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $MonorepoKernel = new MonorepoKernel();
        $MonorepoKernel->boot();

        return $MonorepoKernel->getContainer();
    }
}
