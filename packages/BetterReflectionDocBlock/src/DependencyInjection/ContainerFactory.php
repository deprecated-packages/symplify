<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\DependencyInjection;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface as SymfonyContainerInterface;

final class ContainerFactory
{
    /**
     * @return ContainerInterface|SymfonyContainerInterface|Container
     */
    public function create(): ContainerInterface
    {
        $appKernel = new BetterReflectionDocBlockKernel('dev', true);
        $appKernel->boot();

        return $appKernel->getContainer();
    }
}
