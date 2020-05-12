<?php

declare(strict_types=1);

namespace Symplify\ParameterNameGuard\Tests\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\ParameterNameGuard\ParameterNameGuardBundle;

final class ParameterNameGuardHttpKernel extends Kernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        foreach ($this->configs as $config) {
            $loader->load($config);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/parameter_name_guard';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/parameter_name_guard_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new ParameterNameGuardBundle()];
    }

    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }
}
