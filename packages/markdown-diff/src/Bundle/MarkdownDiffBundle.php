<?php

declare(strict_types=1);

namespace Symplify\MarkdownDiff\Bundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symplify\MarkdownDiff\DependencyInjection\Extension\MarkdownDiffExtension;

final class MarkdownDiffBundle extends Bundle
{
    protected function createContainerExtension(): MarkdownDiffExtension
    {
        return new MarkdownDiffExtension();
    }
}
