<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Adapter\Symfony;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

final class AppKernel extends Kernel
{
    /**
     * @var string
     */
    private $configPath;

    public function __construct(string $configPath)
    {
        $this->configPath = $configPath;
        parent::__construct('dev' . random_int(1, 10000), true);
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
        $loader->load($this->configPath);
    }
}
