<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\HttpKernel;

use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\Composer\VendorDirProvider;
use Symplify\PackageBuilder\Configuration\Loader\NeonLoader;

abstract class AbstractCliKernel extends Kernel
{
    public function __construct()
    {
        parent::__construct(random_int(1, 10000), true);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [];
    }

    protected function registerLocalConfig(LoaderInterface $loader, string $configName): void
    {
        if ($localConfig = $this->getLocalConfigPath($configName)) {
            $loader->load($localConfig);
        }
    }

    protected function getContainerLoader(ContainerInterface $container): DelegatingLoader
    {
        /** @var DelegatingLoader $delegationLoader */
        $delegationLoader = parent::getContainerLoader($container);

        /** @var LoaderResolver $resolver */
        $resolver = $delegationLoader->getResolver();
        $resolver->addLoader(new NeonLoader($container));

        return $delegationLoader;
    }

    /**
     * @return string|false
     */
    private function getLocalConfigPath(string $configName)
    {
        if (file_exists($configName)) {
            return $configName;
        }

        $vendorDir = VendorDirProvider::provide();

        $possibleConfigPaths = [
            $vendorDir . '/../' . $configName,
            getcwd() . '/' . $configName,
        ];

        foreach ($possibleConfigPaths as $possibleConfigPath) {
            if (file_exists($possibleConfigPath)) {
                return $possibleConfigPath;
            }
        }

        return false;
    }
}
