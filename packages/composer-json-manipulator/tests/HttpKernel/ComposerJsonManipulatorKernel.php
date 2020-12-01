<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Tests\HttpKernel;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\ComposerJsonManipulator\Bundle\ComposerJsonManipulatorBundle;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class ComposerJsonManipulatorKernel extends AbstractSymplifyKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new ComposerJsonManipulatorBundle(), new SymplifyKernelBundle()];
    }
}
