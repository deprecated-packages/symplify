<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\TokenRunner\ValueObject;

final class DocBlockEdgeDefinition
{
    public function __construct(
        private int $kind,
        private string $startChar,
        private string $endChar
    ) {
    }

    public function getKind(): int
    {
        return $this->kind;
    }

    public function getStartChar(): string
    {
        return $this->startChar;
    }

    public function getEndChar(): string
    {
        return $this->endChar;
    }
}
