<?php

declare(strict_types=1);

namespace Symplify\ConsoleColorDiff;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;

final class ConsoleColorDiffBundle extends Bundle
{
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new ConsoleColorDiffExtension();
    }
}
