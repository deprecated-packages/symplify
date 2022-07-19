<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class ClassConstRegexMetadata
{
    public function __construct(
        private string $constantName,
        private string $regexValue,
        private string $filePath,
        private int $line
    ) {
    }

    public function getConstantName(): string
    {
        return $this->constantName;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getRegexValue(): string
    {
        return $this->regexValue;
    }

    public function getLine(): int
    {
        return $this->line;
    }
}
