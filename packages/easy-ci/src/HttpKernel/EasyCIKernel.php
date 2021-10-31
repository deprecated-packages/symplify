<?php

declare(strict_types=1);

namespace Symplify\EasyCI\HttpKernel;

use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symplify\Astral\DependencyInjection\Extension\AstralExtension;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonManipulatorConfig;
use Symplify\ConfigTransformer\Exception\ShouldNotHappenException;
use Symplify\SymfonyContainerBuilder\ContainerBuilderFactory;
use Symplify\SymplifyKernel\Contract\LightKernelInterface;
use Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension;

final class EasyCIKernel implements LightKernelInterface
{
    private Container|null $container = null;

    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $containerBuilderFactory = new ContainerBuilderFactory();

        $extensions = [new SymplifyKernelExtension(), new AstralExtension()];
        $compilerPasses = [new AutowireArrayParameterCompilerPass()];

        $configFiles[] = __DIR__ . '/../../config/config.php';
        $configFiles[] = ComposerJsonManipulatorConfig::FILE_PATH;

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
