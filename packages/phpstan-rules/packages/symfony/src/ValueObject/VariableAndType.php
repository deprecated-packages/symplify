<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ValueObject;

use PHPStan\Type\Type;

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
}
