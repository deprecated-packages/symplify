<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\ValueObject;

use Symplify\LattePHPStanCompiler\Contract\ValueObject\CallReferenceInterface;

final class DynamicCallReference implements CallReferenceInterface
{
    public function __construct(
        private string $class,
        private string $method
    ) {
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getMethod(): string
    {
        return $this->method;
    }
}
