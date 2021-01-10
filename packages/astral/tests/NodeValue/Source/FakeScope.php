<?php

declare(strict_types=1);

namespace Symplify\Astral\Tests\NodeValue\Source;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ConstantReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\PropertyReflection;

final class FakeScope implements Scope
{
    public function isInClass(): bool
    {

    }

    public function getClassReflection(): ?\PHPStan\Reflection\ClassReflection
    {

    }

    public function canAccessProperty(PropertyReflection $propertyReflection): bool
    {

    }

    public function canCallMethod(MethodReflection $methodReflection): bool
    {

    }

    public function canAccessConstant(ConstantReflection $constantReflection): bool
    {

    }

    public function getFile(): string
    {

    }

    public function getFileDescription(): string
    {

    }

    public function isDeclareStrictTypes(): bool
    {

    }

    public function isInTrait(): bool
    {

    }

    public function getTraitReflection(): ?\PHPStan\Reflection\ClassReflection
    {

    }

    public function getFunction()
    {

    }

    public function getFunctionName(): ?string
    {

    }

    public function getNamespace(): ?string
    {

    }

    public function getParentScope(): ?Scope
    {

    }

    public function hasVariableType(string $variableName): \PHPStan\TrinaryLogic
    {

    }

    public function getVariableType(string $variableName): \PHPStan\Type\Type
    {

    }

    public function getDefinedVariables(): array
    {

    }

    public function hasConstant(Name $name): bool
    {

    }

    public function isInAnonymousFunction(): bool
    {

    }

    public function getAnonymousFunctionReflection(): ?\PHPStan\Reflection\ParametersAcceptor
    {

    }

    public function getAnonymousFunctionReturnType(): ?\PHPStan\Type\Type
    {

    }

    public function getType(Expr $node): \PHPStan\Type\Type
    {

    }

    public function getNativeType(Expr $expr): \PHPStan\Type\Type
    {

    }

    public function doNotTreatPhpDocTypesAsCertain(): Scope
    {

    }

    public function resolveName(Name $name): string
    {

    }

    public function getTypeFromValue($value): \PHPStan\Type\Type
    {

    }

    public function isSpecified(Expr $node): bool
    {

    }

    public function isInClassExists(string $className): bool
    {

    }

    public function isInClosureBind(): bool
    {

    }

    public function isParameterValueNullable(Param $parameter): bool
    {

    }

    public function getFunctionType($type, bool $isNullable, bool $isVariadic): \PHPStan\Type\Type
    {

    }

    public function isInExpressionAssign(Expr $expr): bool
    {

    }

    public function filterByTruthyValue(Expr $expr, bool $defaultHandleFunctions = \false): Scope
    {

    }

    public function filterByFalseyValue(Expr $expr, bool $defaultHandleFunctions = \false): Scope
    {

    }

    public function isInFirstLevelStatement(): bool
    {

    }
}
