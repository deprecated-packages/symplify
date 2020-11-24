<?php

declare(strict_types=1);

namespace Symplify\PHPMDDecomposer\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;

final class PHPMDDecomposerKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/phpmd_decomposer';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/phpmd_decomposer_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new SymplifyKernelBundle(), new PhpConfigPrinterBundle()];
    }
}
