<?php

declare(strict_types=1);

namespace Symplify\SymplifyKernel\Contract;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

interface SimplifyBundleInterface
{
    public function build(ContainerBuilder $containerBuilder): void;

    public function provideContainerExtension(): ExtensionInterface|null;
}
