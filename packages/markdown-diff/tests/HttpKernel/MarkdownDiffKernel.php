<?php

declare(strict_types=1);

namespace Symplify\MarkdownDiff\Tests\HttpKernel;

use Psr\Container\ContainerInterface;
use Symplify\MarkdownDiff\ValueObject\MarkdownDiffConfig;
use Symplify\SymplifyKernel\HttpKernel\AbstractSymplifyKernel;

final class MarkdownDiffKernel extends AbstractSymplifyKernel
{
    public function createFromConfigs(array $configFiles): ContainerInterface
    {
        $configFiles[] = MarkdownDiffConfig::FILE_PATH;
        return $this->create([], [], $configFiles);
    }
}
