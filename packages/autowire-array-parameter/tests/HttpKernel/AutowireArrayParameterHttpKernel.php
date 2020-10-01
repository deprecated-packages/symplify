<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\Tests\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;

final class AutowireArrayParameterHttpKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/autowire_array_parameter.php');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/autowire_array_parameter_test';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/autowire_array_parameter_test_log';
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
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }
}
