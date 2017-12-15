<?php declare(strict_types=1);

namespace Symplify\GitWrapper\DependencyInjection;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    /**
     * @var null|string
     */
    private $configFile;

    public function __construct(?string $configConfig = '')
    {
        $this->configFile = $configConfig;

        // random_int is used to prevent container name duplication during tests
        parent::__construct((string) random_int(1, 1000000), false);
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../config/config.yml');

        if ($this->configFile) {
            $loader->load($this->configFile);
        }
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/_gitwrapper_kernel';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [];
    }
}
