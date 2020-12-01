<?php

declare(strict_types=1);

namespace Symplify\Psr4Switcher\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\ComposerJsonManipulator\Bundle\ComposerJsonManipulatorBundle;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class Psr4SwitcherKernel extends AbstractSymplifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new ComposerJsonManipulatorBundle(), new SymplifyKernelBundle()];
    }
}
