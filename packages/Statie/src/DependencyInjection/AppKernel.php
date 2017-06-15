<?php declare(strict_types=1);

namespace Symplify\Statie\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\Statie\DependencyInjection\CompilerPass\CollectorCompilerPass;

final class AppKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct(random_int(1, 1000), true);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_statie';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new CollectorCompilerPass);
    }
}
