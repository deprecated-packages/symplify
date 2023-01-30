<?php

declare(strict_types=1);

namespace Symplify\SymfonyStaticDumper\ValueObject;

final class ControllerCallable
{
    public function __construct(
        private readonly string $class,
        private readonly string $method
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
