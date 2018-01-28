<?php declare(strict_types=1);

namespace Symplify\Monorepo;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\DependencyInjection\DefinitionCollector;

final class MonorepoKernel extends Kernel implements CompilerPassInterface
{
    public function __construct()
    {
        parent::__construct('dev', true);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/services.yml');
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        $this->loadCommandsToApplication($containerBuilder);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_symplify_Monorepo_cache';
    }

    public function getLogDir()
    {
        return sys_get_temp_dir() . '/_symplify_Monorepo_log';
    }

    private function loadCommandsToApplication(ContainerBuilder $containerBuilder): void
    {
        DefinitionCollector::loadCollectorWithType(
            $containerBuilder,
            Application::class,
            Command::class,
            'add'
        );
    }
}
