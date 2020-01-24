<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\PackageBuilder\Parameter\ParameterProvider;

final class PackageBuilderTestKernel extends Kernel implements ExtraConfigAwareKernelInterface
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

    /**
     * @param string[] $configs
     */
    public function setConfigs(array $configs): void
    {
        $this->configs = $configs;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/package_builder';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/package_builder_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [];
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->autowire(ParameterProvider::class)
            ->setPublic(true);
    }
}
