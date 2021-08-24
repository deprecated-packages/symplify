<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ValueObject;

final class VariableAndMissingMethodName
{
    public function __construct(
        private string $variableName,
        private string $variableTypeClassName,
        private string $methodName
    ) {
    }

    public function getVariableName(): string
    {
        return $this->variableName;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getVariableTypeClassName(): string
    {
        return $this->variableTypeClassName;
    }
}
