<?php

declare(strict_types=1);

namespace Symplify\TemplatePHPStanCompiler\ValueObject;

use PHPStan\Type\Type;
use PHPStan\Type\VerbosityLevel;

final class VariableAndType
{
    public function __construct(
        private string $variable,
        private Type $type
    ) {
    }

    public function getVariable(): string
    {
        return $this->variable;
    }

    public function getType(): Type
    {
        return $this->type;
    }

    public function getTypeAsString(): string
    {
        return $this->type->describe(VerbosityLevel::typeOnly());
    }
}
