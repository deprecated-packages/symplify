<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\CompilerPass\PublicForTestsCompilerPass;

final class BetterReflectionDocBlockKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/services.yml');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_better_reflection_doc_block';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/_better_reflection_doc_block_logs';
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
        $containerBuilder->addCompilerPass(new PublicForTestsCompilerPass());
    }
}
