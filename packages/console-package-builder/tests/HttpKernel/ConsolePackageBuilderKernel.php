<?php

declare(strict_types=1);

namespace Symplify\ConsolePackageBuilder\Tests\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ConsolePackageBuilder\Bundle\NamelessConsoleCommandBundle;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;

final class ConsolePackageBuilderKernel extends Kernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new NamelessConsoleCommandBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
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
        return sys_get_temp_dir() . '/console_package_builder';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/console_package_builder_log';
    }
}
