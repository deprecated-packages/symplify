<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class MethodCallReference
{
    public function __construct(
        private string $class,
        private string $methodName
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }
}
