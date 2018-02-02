<?php declare(strict_types=1);

namespace Symplify\Monorepo\HttpKernel;

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
    /**
     * @var null|string
     */
    private $config;

    public function __construct(?string $config = null)
    {
        $this->config = $config;

        // random_int is used to prevent container name duplication during tests
        parent::__construct((string) random_int(1, 1000000), true);
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
        $loader->load(__DIR__ . '/../config/services.yml');
        if ($this->config) {
            $loader->load($this->config);
        }
    }

    public function process(ContainerBuilder $containerBuilder): void
    {
        $this->loadCommandsToApplication($containerBuilder);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_symplify_monorepo_cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/_symplify_monorepo_log';
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
