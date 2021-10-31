<?php

declare(strict_types=1);

namespace Symplify\EasyTesting\HttpKernel;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\ConfigTransformer\Exception\ShouldNotHappenException;
use Symplify\SymfonyContainerBuilder\ContainerBuilderFactory;
use Symplify\SymplifyKernel\Contract\LightKernelInterface;
use Symplify\SymplifyKernel\ValueObject\SymplifyKernelConfig;

final class EasyTestingKernel implements LightKernelInterface
{
    private Container|null $container = null;

    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $containerBuilderFactory = new ContainerBuilderFactory();

        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = SymplifyKernelConfig::FILE_PATH;

        $compilerPasses = [new AutowireArrayParameterCompilerPass()];

        $containerBuilder = $containerBuilderFactory->create([], $compilerPasses, $configFiles);
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
