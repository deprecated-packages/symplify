<?php

declare(strict_types=1);

namespace Symplify\LattePHPStanCompiler\ValueObject;

use Symplify\LattePHPStanCompiler\Contract\ValueObject\CallReferenceInterface;

final class FunctionCallReference implements CallReferenceInterface
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
