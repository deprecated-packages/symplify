<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\HttpKernel;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\Configuration\Loader\NeonLoader;

abstract class AbstractCliKernel extends Kernel
{
    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        /** @var DelegatingLoader $delegationLoader */
        $delegationLoader = parent::getContainerLoader($container);

        /** @var LoaderResolver $resolver */
        $resolver = $delegationLoader->getResolver();
        $resolver->addLoader(new NeonLoader($container));

        return $delegationLoader;
    }
}
