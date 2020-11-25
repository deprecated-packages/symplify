<?php

declare(strict_types=1);

namespace Symplify\SymfonyRouteUsage;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\SymfonyRouteUsage\DependencyInjection\SymfonyRouteUsageExtension;

final class SymfonyRouteUsageBundle extends Bundle
{
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new SymfonyRouteUsageExtension();
    }
}
