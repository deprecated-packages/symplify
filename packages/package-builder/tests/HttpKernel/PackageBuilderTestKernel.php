<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Tests\HttpKernel;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class PackageBuilderTestKernel extends AbstractSymplifyKernel
{
    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->autowire(ParameterProvider::class)
            ->setPublic(true);
    }
}
