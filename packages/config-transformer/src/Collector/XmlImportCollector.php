<?php

declare(strict_types=1);

namespace Symplify\ConfigTransformer\Collector;

use Symplify\PhpConfigPrinter\ValueObject\YamlKey;

final class XmlImportCollector
{
    /**
     * @var array<array{resource: mixed, ignore_errors: bool|string}>
     */
    private array $imports = [];

    public function addImport(mixed $resource, bool|string $ignoreErrors): void
    {
        $this->imports[] = [
            YamlKey::RESOURCE => $resource,
            YamlKey::IGNORE_ERRORS => $ignoreErrors,
        ];
    }

    /**
     * @return array<array{resource: mixed, ignore_errors: bool|string}>
     */
    public function provide(): array
    {
        return $this->imports;
    }
}
