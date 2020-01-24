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
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\AutoReturnFactoryCompilerPass;

final class ChangelogLinkerKernel extends Kernel implements ExtraConfigAwareKernelInterface
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
        // needs to be first, since it's adding new service definitions
        $containerBuilder->addCompilerPass(new AutoReturnFactoryCompilerPass());

        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
        $containerBuilder->addCompilerPass(new AddRepositoryUrlAndRepositoryNameParametersCompilerPass());
        $containerBuilder->addCompilerPass(new AutoBindParameterCompilerPass());
    }
}
