<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\HttpKernel;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\SymplifyKernel\Config\Loader\ParameterMergingLoaderFactory;
use Symplify\SymplifyKernel\ContainerBuilderFactory;
use Symplify\SymplifyKernel\Contract\LightKernelInterface;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
use Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;

/**
 * @api
 */
abstract class AbstractSymplifyKernel implements LightKernelInterface
{
    private Container|null $container = null;

    /**
     * @param string[] $configFiles
     * @param CompilerPassInterface[] $compilerPasses
     * @param ExtensionInterface[] $extensions
     */
    public function create(array $configFiles, array $compilerPasses = [], array $extensions = []): ContainerInterface
    {
        $containerBuilderFactory = new ContainerBuilderFactory(new ParameterMergingLoaderFactory());

        $compilerPasses[] = new AutowireArrayParameterCompilerPass();

        $configFiles[] = SymplifyKernelConfig::FILE_PATH;

        $containerBuilder = $containerBuilderFactory->create($configFiles, $compilerPasses, $extensions);
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
