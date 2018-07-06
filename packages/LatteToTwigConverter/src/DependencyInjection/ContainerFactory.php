<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\DependencyInjection;

use Psr\Container\ContainerInterface;

final class ContainerFactory
{
    public function create(): ContainerInterface
    {
        $appKernel = new LatteToTwigConverterKernel();
        $appKernel->boot();

        return $appKernel->getContainer();
    }
}
