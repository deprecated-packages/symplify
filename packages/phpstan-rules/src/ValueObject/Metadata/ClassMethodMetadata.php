<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject\Metadata;

final class ClassMethodMetadata
{
    public function __construct(
        private string $methodName,
        private int $lineCount,
        private string $fileName,
        private int $line,
    ) {
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getLineCount(): int
    {
        return $this->lineCount;
    }
}
