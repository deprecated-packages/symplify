<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\ValueObject;

final class FunctionCallReference
{
    public function __construct(
        private string $function
    ) {
    }

    public function getFunction(): string
    {
        return $this->function;
    }
}
