<?php

declare(strict_types=1);

namespace Symplify\MarkdownDiff;

use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\MarkdownDiff\DependencyInjection\Extension\MarkdownDiffExtension;

final class MarkdownDiffBundle extends Bundle
{
    protected function createContainerExtension(): ?ExtensionInterface
    {
        return new MarkdownDiffExtension();
    }
}
