<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\EasyHydrator\DependencyInjection\Extension\EasyHydratorExtension;

final class EasyHydratorBundle extends Bundle
{
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new EasyHydratorExtension();
    }
}
