<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\AutowireArrayParameter\DependencyInjection\CompilerPass\AutowireArrayParameterCompilerPass;
use Symplify\SymplifyKernel\Contract\SimplifyBundleInterface;
use Symplify\SymplifyKernel\DependencyInjection\Extension\SymplifyKernelExtension;

final class SymplifyKernelBundle implements SimplifyBundleInterface
{
    public function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->addCompilerPass(new AutowireArrayParameterCompilerPass());
    }

    public function provideContainerExtension(): SymplifyKernelExtension
    {
        return new SymplifyKernelExtension();
    }
}
