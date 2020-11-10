<?php

declare(strict_types=1);

namespace Symplify\MarkdownDiff\Tests\HttpKernel;

use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\MarkdownDiff\MarkdownDiffBundle;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class MarkdownDiffKernel extends AbstractSymplifyKernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new MarkdownDiffBundle(), new SymplifyKernelBundle()];
    }
}
