<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\Tests\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;

final class TokenRunnerKernel extends Kernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.yaml');

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
        return sys_get_temp_dir() . '/token_runner';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/token_runner_log';
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
        // needed for by "symplify/better-phpdoc-parser"
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }
}
