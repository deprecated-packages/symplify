<?php

declare(strict_types=1);

namespace Symplify\ChangelogLinker\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\AutoBindParameter\DependencyInjection\CompilerPass\AutoBindParameterCompilerPass;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\ChangelogLinker\DependencyInjection\CompilerPass\AddRepositoryUrlAndRepositoryNameParametersCompilerPass;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ChangelogLinkerKernel extends Kernel implements ExtraConfigAwareKernelInterface
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
            $this->configs[] = $config instanceof SmartFileInfo ? $config->getRealPath() : $config;
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/changelog_linker';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/changelog_linker_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [];
    }

    /**
     * Order matters!
     */
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
        $containerBuilder->addCompilerPass(new AddRepositoryUrlAndRepositoryNameParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParameterCompilerPass());
    }
}
