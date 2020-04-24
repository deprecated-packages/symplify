<?php

declare(strict_types=1);

namespace Symplify\ComposerJsonManipulator\Tests\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\ComposerJsonManipulator\ComposerJsonManipulatorBundle;

final class ComposerJsonManipulatorKernel extends Kernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [new ComposerJsonManipulatorBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/composer_json_manipulator_test';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/composer_json_manipulator_test_log';
    }
}
