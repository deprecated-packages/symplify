<?php

declare(strict_types=1);

namespace Symplify\SymfonyContainerBuilder;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symplify\SymfonyContainerBuilder\Config\Loader\ParameterMergingLoaderFactory;
use Symplify\SymfonyContainerBuilder\DependencyInjection\LoadExtensionConfigsCompilerPass;
use Symplify\SymfonyContainerBuilder\Parameters\KernelParametersProvider;
use Webmozart\Assert\Assert;

final class ContainerBuilderFactory
{
    private ParameterMergingLoaderFactory $parameterMergingLoaderFactory;

    private KernelParametersProvider $kernelParameterProvider;

    public function __construct()
    {
        $this->parameterMergingLoaderFactory = new ParameterMergingLoaderFactory();
        $this->kernelParameterProvider = new KernelParametersProvider();
    }

    /**
     * @param ExtensionInterface[] $extensions
     * @param CompilerPassInterface[] $compilerPasses
     * @param string[] $configFiles
     */
    public function create(array $extensions, array $compilerPasses, array $configFiles): ContainerBuilder
    {
        Assert::allString($configFiles);
        Assert::allFile($configFiles);

        $containerBuilder = new ContainerBuilder();

        $this->registerExtensions($containerBuilder, $extensions);
        $this->registerConfigFiles($containerBuilder, $configFiles);
        $this->registerCompilerPasses($containerBuilder, $compilerPasses);

        $kernelParameters = $this->kernelParameterProvider->provide();
        foreach ($kernelParameters as $name => $value) {
            $containerBuilder->setParameter($name, $value);
        }

        // this calls load() method in every extensions
        // ensure these extensions are implicitly loaded
        $compilerPassConfig = $containerBuilder->getCompilerPassConfig();
        $compilerPassConfig->setMergePass(new LoadExtensionConfigsCompilerPass());

        return $containerBuilder;
    }

    /**
     * @param ExtensionInterface[] $extensions
     */
    private function registerExtensions(ContainerBuilder $containerBuilder, array $extensions): void
    {
        foreach ($extensions as $extension) {
            $containerBuilder->registerExtension($extension);
        }
    }

    /**
     * @param CompilerPassInterface[] $compilerPasses
     */
    private function registerCompilerPasses(ContainerBuilder $containerBuilder, array $compilerPasses): void
    {
        foreach ($compilerPasses as $compilerPass) {
            $containerBuilder->addCompilerPass($compilerPass);
        }
    }

    /**
     * @param string[] $configFiles
     */
    private function registerConfigFiles(ContainerBuilder $containerBuilder, array $configFiles): void
    {
        $delegatingLoader = $this->parameterMergingLoaderFactory->create($containerBuilder, getcwd());
        foreach ($configFiles as $configFile) {
            $delegatingLoader->load($configFile);
        }
    }
}
