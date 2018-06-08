<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\DependencyInjection;

use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $appKernel = new MonorepoBuilderKernel();
        $appKernel->boot();

        return $appKernel->getContainer();
    }
}
