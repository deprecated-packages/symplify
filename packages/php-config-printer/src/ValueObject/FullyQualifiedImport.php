<?php

declare(strict_types=1);

namespace Symplify\PhpConfigPrinter\ValueObject;

use Stringable;

final class FullyQualifiedImport implements Stringable
{
    /**
     * @param ImportType::* $type
     */
    public function __construct(
        private readonly string $type,
        private readonly string $fullyQualified,
        private readonly string $shortClassName
    ) {
    }

    public function __toString(): string
    {
        return $this->fullyQualified;
    }

    /**
     * @return ImportType::*
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getFullyQualified(): string
    {
        return $this->fullyQualified;
    }

    public function getShortClassName(): string
    {
        return $this->shortClassName;
    }
}
