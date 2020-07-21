<?php

declare(strict_types=1);

namespace Symplify\MonorepoBuilder\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\AutoBindParameter\DependencyInjection\CompilerPass\AutoBindParameterCompilerPass;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\ComposerJsonManipulator\ComposerJsonManipulatorBundle;
use Symplify\MonorepoBuilder\Release\Contract\ReleaseWorker\ReleaseWorkerInterface;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutowireInterfacesCompilerPass;
use Symplify\SmartFileSystem\SmartFileInfo;

final class MonorepoBuilderKernel extends Kernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');

        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }

    /**
     * @param string[]|SmartFileInfo[] $configs
     */
    public function setConfigs(array $configs): void
    {
        foreach ($configs as $config) {
            if ($config instanceof SmartFileInfo) {
                $this->configs[] = $config->getRealPath();
            } else {
                $this->configs[] = $config;
            }
        }
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
        return [new ComposerJsonManipulatorBundle()];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireInterfacesCompilerPass([ReleaseWorkerInterface::class]));
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParameterCompilerPass());
    }
}
