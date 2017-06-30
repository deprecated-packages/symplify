<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Neon;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\PackageBuilder\Neon\Loader\NeonLoader;

/**
 * This trait allows to load *.neon files in Kernel.
 * Use in descendants of @see \Symfony\Component\HttpKernel\Kernel.
 */
trait NeonLoaderAwareKernelTrait
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
