<?php declare(strict_types=1);

namespace Symplify\LatteToTwigConverter\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoReturnFactoryCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\ConfigurableCollectorCompilerPass;
use Symplify\PackageBuilder\HttpKernel\SimpleKernelTrait;

final class LatteToTwigConverterKernel extends Kernel
{
    use SimpleKernelTrait;

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.yml');
    }

    /**
     * Order matters!
     */
    protected function build(ContainerBuilder $containerBuilder): void
    {
        // needs to be first, since it's adding new service definitions
        $containerBuilder->addCompilerPass(new AutoReturnFactoryCompilerPass());

        $containerBuilder->addCompilerPass(new ConfigurableCollectorCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }
}
