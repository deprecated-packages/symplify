<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class VariableNameMetadata
{
    public function __construct(
        private string $variableName,
        private string $filePath,
        private int $lineNumber
    ) {
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }
}
