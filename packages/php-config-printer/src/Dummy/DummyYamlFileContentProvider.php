<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\Dummy;

use Symplify\PhpConfigPrinter\Contract\YamlFileContentProviderInterface;

final class DummyYamlFileContentProvider implements YamlFileContentProviderInterface
{
    public function setContent(string $yamlContent): void
    {
    }

    public function getYamlContent(): string
    {
        return '';
    }
}
