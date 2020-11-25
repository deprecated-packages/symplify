<?php

declare(strict_types=1);

namespace Symplify\StaticDetector\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class StaticDetectorKernel extends AbstractSymplifyKernel implements ExtraConfigAwareKernelInterface
{
    /**
     * @var string[]
     */
    private $configs = [];

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');

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
}
