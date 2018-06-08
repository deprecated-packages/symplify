<?php declare(strict_types=1);

namespace Symplify\MonorepoBuilder\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\MonorepoBuilder\DependencyInjection\CompilerPass\CollectorCompilerPass;
use Symplify\PackageBuilder\HttpKernel\AbstractCliKernel;

final class MonorepoBuilderKernel extends AbstractCliKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config.yml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_MonorepoBuilder_linker';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/_MonorepoBuilder_linker_logs';
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass());
    }
}
