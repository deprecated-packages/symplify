<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests\HttpKernel;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\EasyHydrator\EasyHydratorBundle;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class EasyHydratorTestKernel extends AbstractSymplifyKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new EasyHydratorBundle(), new SymplifyKernelBundle()];
    }
}
