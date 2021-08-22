<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ValueObject;

final class VariableAndMissingMethodName
{
    public function __construct(
        private string $variable,
        private string $methodName
    ) {
    }

    public function getVariable(): string
    {
        return $this->variable;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }
}
