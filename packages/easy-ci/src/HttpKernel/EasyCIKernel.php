<?php

declare(strict_types=1);

namespace Symplify\EasyCI\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\Astral\Bundle\AstralBundle;
use Symplify\ComposerJsonManipulator\Bundle\ComposerJsonManipulatorBundle;
use Symplify\PackageBuilder\Contract\HttpKernel\ExtraConfigAwareKernelInterface;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class EasyCIKernel extends AbstractSymplifyKernel implements ExtraConfigAwareKernelInterface
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');

        parent::registerContainerConfiguration($loader);
    }

    /**
     * @return iterable<BundleInterface>
     */
    public function registerBundles(): iterable
    {
        return [new ComposerJsonManipulatorBundle(), new SymplifyKernelBundle(), new AstralBundle()];
    }
}
