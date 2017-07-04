<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\HttpKernel;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PackageBuilder\Neon\NeonLoaderAwareKernelTrait;

abstract class AbstractCliKernel extends Kernel
{
    use NeonLoaderAwareKernelTrait;

    public function __construct()
    {
        // random_int is used to prevent container name duplication during tests
        parent::__construct(random_int(1, 10000), true);
    }

    /**
     * Default method to prevent forcing using it
     * when no bundles are needed.
     *
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [];
    }
}
