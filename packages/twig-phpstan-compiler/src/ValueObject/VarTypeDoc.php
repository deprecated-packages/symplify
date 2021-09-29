<?php

declare(strict_types=1);

namespace Symplify\TwigPHPStanCompiler\ValueObject;

final class VarTypeDoc
{
    public function __construct(
        private string $variableName,
        private string $type
    ) {
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
