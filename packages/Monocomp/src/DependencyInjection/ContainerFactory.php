<?php declare(strict_types=1);

namespace Symplify\Monocomp\DependencyInjection;

use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $appKernel = new MonocompKernel();
        $appKernel->boot();

        return $appKernel->getContainer();
    }
}
