<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\HttpKernel;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

abstract class AbstractCliKernel extends Kernel
{
    public function __construct()
    {
        // random_int is used to prevent container name duplication during tests
        parent::__construct((string) random_int(1, 100000000), false);
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
