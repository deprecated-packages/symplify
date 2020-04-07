<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

final class EasyHydratorTestKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.yaml');
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [];
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/eay_hydrator_test';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/eay_hydrator_test_log';
    }
}
