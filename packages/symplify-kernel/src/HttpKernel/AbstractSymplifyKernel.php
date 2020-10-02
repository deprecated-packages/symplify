<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\Strings\KernelUniqueHasher;

abstract class AbstractSymplifyKernel extends Kernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/' . $this->getUniqueKernelHash();
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/' . $this->getUniqueKernelHash() . '_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new SymplifyKernelBundle()];
    }

    /**
     * @param string[]|SmartFileInfo[] $configs
     */
    public function setConfigs(array $configs): void
    {
        foreach ($configs as $config) {
            if ($config instanceof SmartFileInfo) {
                $config = $config->getRealPath();
            }

            $this->configs[] = $config;
        }
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }

    private function getUniqueKernelHash(): string
    {
        return (new KernelUniqueHasher())->hashKernelClass(static::class);
    }
}
