<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\HttpKernel;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\SymfonyContainerBuilder\ContainerBuilderFactory;
use Symplify\SymplifyKernel\Contract\LightKernelInterface;
use Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

/**
 * @api
 */
abstract class AbstractSymplifyKernel implements LightKernelInterface
{
    private Container|null $container = null;

    /**
     * @param string[] $configFiles
     */
    public function create(array $extensions, array $compilerPasses, array $configFiles): ContainerInterface
    {
        $containerBuilderFactory = new ContainerBuilderFactory();

        $extensions[] = new SymplifyKernelExtension();
        $compilerPasses[] = new AutowireArrayParameterCompilerPass();

        $containerBuilder = $containerBuilderFactory->create($extensions, $compilerPasses, $configFiles);
        $containerBuilder->compile();

        $this->container = $containerBuilder;

        return $containerBuilder;
    }

    public function getContainer(): ContainerInterface
    {
        if (! $this->container instanceof Container) {
            throw new ShouldNotHappenException();
        }

        return $this->container;
    }
}
