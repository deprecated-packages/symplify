<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\MonorepoBuilder\Split\DependencyInjection\CompilerPass\DetectParametersCompilerPass;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoBindParametersCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoReturnFactoryCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;

final class MonorepoBuilderKernel extends Kernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.yaml');

        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }

    /**
     * @param string[] $configs
     */
    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/monorepo_builder';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/monorepo_builder_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        // needs to be first, since it's adding new service definitions
        $containerBuilder->addCompilerPass(new AutoReturnFactoryCompilerPass());

        $containerBuilder->addCompilerPass(new AutowireInterfacesCompilerPass([ReleaseWorkerInterface::class]));

        $containerBuilder->addCompilerPass(new DetectParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParametersCompilerPass());
    }
}
