<?php declare(strict_types=1);

namespace Symplify\Statie\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\HttpKernel\AbstractCliKernel;
use Symplify\Statie\DependencyInjection\CompilerPass\CollectorCompilerPass;

final class AppKernel extends AbstractCliKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');
        $this->registerLocalConfig($loader, 'statie.neon');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_statie';
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass);
    }
}
