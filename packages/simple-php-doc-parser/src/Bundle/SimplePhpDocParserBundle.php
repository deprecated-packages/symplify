<?php

declare(strict_types=1);

namespace Symplify\SimplePhpDocParser\Bundle;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\SimplePhpDocParser\Bundle\DependencyInjection\Extension\SimplePhpDocParserExtension;

final class SimplePhpDocParserBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new SimplePhpDocParserExtension();
    }
}
