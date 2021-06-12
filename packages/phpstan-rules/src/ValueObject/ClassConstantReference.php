<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class ClassConstantReference
{
    public function __construct(
        private string $class,
        private string $constant
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getConstant(): string
    {
        return $this->constant;
    }
}
