<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule\Fixture;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

final class SkipKernelProtectedMethod extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles()
    {
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
    }


    protected function configureRoutes(RouteCollectionBuilder $routeCollectionBuilder)
    {

    }

    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader)
    {
    }
}
