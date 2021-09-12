<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject;

final class PhpFileContentsWithLineMap
{
    /**
     * @param array<int, int> $phpToTemplateLines
     */
    public function __construct(
        private string $phpFileContents,
        private array $phpToTemplateLines
    ) {
    }

    public function getPhpFileContents(): string
    {
        return $this->phpFileContents;
    }

    /**
     * @return array<int, int>
     */
    public function getPhpToTemplateLines(): array
    {
        return $this->phpToTemplateLines;
    }
}
