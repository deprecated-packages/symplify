<?php

declare(strict_types=1);

namespace Symplify\RuleDocGenerator\HttpKernel;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symplify\MarkdownDiff\Bundle\MarkdownDiffBundle;
use Symplify\PhpConfigPrinter\Bundle\PhpConfigPrinterBundle;
use Symplify\SymplifyKernel\Bundle\SymplifyKernelBundle;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class RuleDocGeneratorKernel extends AbstractSymplifyKernel
{
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/../../config/config.php');

        parent::registerContainerConfiguration($loader);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): iterable
    {
        return [new SymplifyKernelBundle(), new MarkdownDiffBundle(), new PhpConfigPrinterBundle()];
    }
}
