<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\LattePHPStanPrinter\ValueObject;

final class StaticCallReference
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
