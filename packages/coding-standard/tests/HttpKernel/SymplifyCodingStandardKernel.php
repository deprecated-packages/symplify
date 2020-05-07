<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\CodingStandard\SymplifyCodingStandardBundle;
use Symplify\ConsoleColorDiff\ConsoleColorDiffBundle;
use Symplify\EasyCodingStandard\EasyCodingStandardBundle;

final class SymplifyCodingStandardKernel extends Kernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/symplify_coding_standard';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/symplify_coding_standard_log';
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new SymplifyCodingStandardBundle(), new EasyCodingStandardBundle(), new ConsoleColorDiffBundle()];
    }
}
