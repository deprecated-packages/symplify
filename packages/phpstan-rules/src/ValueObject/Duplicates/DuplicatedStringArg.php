<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject\Duplicates;

final class DuplicatedStringArg
{
    public function __construct(
        private string $value,
        private int $count
    ) {
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
