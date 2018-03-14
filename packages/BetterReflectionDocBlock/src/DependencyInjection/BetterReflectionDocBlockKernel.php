<?php declare(strict_types=1);

namespace Symplify\BetterReflectionDocBlock\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

final class BetterReflectionDocBlockKernel extends Kernel
{
<<<<<<< HEAD
=======
    /**
     * @var null|string
     */
    private $configFile;

>>>>>>> [BetterReflectionDocBlock] add DI container
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
}
