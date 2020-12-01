<?php

declare(strict_types=1);

namespace Symplify\PackageScoper\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symplify\ComposerJsonManipulator\Bundle\ComposerJsonManipulatorBundle;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class PackageScoperKernel extends AbstractSymplifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');

        parent::registerContainerConfiguration($loader);
    }

    /**
     * @return ComposerJsonManipulatorBundle[]|SymplifyKernelBundle[]
     */
    public function registerBundles(): iterable
    {
        return [new SymplifyKernelBundle(), new ComposerJsonManipulatorBundle()];
    }
}
