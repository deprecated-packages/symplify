<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\Neon\NeonLoaderAwareKernelTraitSource;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

final class NeonFreeKernel extends Kernel
{
    /**
     * @var string
     */
    private $config;

    public function __construct(string $config)
    {
        $this->config = $config;
        parent::__construct('kernel_without_neon', true);
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
        $loader->load($this->config);
    }
}
