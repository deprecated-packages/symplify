<?php

declare(strict_types=1);

namespace Symplify\PHPUnitUpgrader\ValueObject;

final class FileLine
{
    public function __construct(
        private string $filePath,
        private int $line
    ) {
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
