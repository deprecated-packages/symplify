<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ValueObject;

use Stringable;

final class FullyQualifiedImport implements Stringable
{
    public function __construct(
        private string $type,
        private string $fullyQualified,
    ) {
    }

    public function __toString(): string
    {
        return $this->fullyQualified;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getFullyQualified(): string
    {
        return $this->fullyQualified;
    }
}
