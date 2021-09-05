<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenBinaryMethodCallRule\Source;

abstract class SomeAbstractSearch
{
    public function __construct(
        private int|null $id = null
    ) {
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function hasId(): bool
    {
        return $this->id !== null;
    }
}
