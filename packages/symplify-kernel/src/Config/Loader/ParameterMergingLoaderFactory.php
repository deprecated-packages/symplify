<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Config\Loader;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\GlobFileLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\DependencyInjection\FileLoader\ParameterMergingPhpFileLoader;
use Symplify\SymplifyKernel\Contract\Config\LoaderFactoryInterface;

final class ParameterMergingLoaderFactory implements LoaderFactoryInterface
{
    public function create(ContainerBuilder $containerBuilder, string $currentWorkingDirectory): DelegatingLoader
    {
        $fileLocator = new FileLocator([$currentWorkingDirectory]);

        $loaders = [
            new GlobFileLoader($fileLocator),
            new ParameterMergingPhpFileLoader($containerBuilder, $fileLocator),
        ];

        $loaderResolver = new LoaderResolver($loaders);

        return new DelegatingLoader($loaderResolver);
    }
}
