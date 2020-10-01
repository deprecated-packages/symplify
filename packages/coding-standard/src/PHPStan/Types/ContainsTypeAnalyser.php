<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\PHPStan\Types;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use PHPStan\Type\ArrayType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeWithClassName;
use PHPStan\Type\UnionType;

final class ContainsTypeAnalyser
{
    /**
     * @param class-string[] $types
     */
    public function containsExprTypes(Expr $expr, Scope $scope, array $types): bool
    {
        foreach ($types as $type) {
            if (! $this->containsExprType($expr, $scope, $type)) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function containsExprType(Expr $expr, Scope $scope, string $type): bool
    {
        $propertyType = $scope->getType($expr);
        if ($propertyType instanceof TypeWithClassName) {
            return is_a($propertyType->getClassName(), $type, true);
        }

        if ($this->isUnionTypeWithClass($propertyType, $type)) {
            return true;
        }

        return $this->isArrayWithItemType($propertyType, $type);
    }

    private function isUnionTypeWithClass(Type $type, string $class): bool
    {
        if (! $type instanceof UnionType) {
            return false;
        }

        foreach ($type->getTypes() as $unionedType) {
            if (! $unionedType instanceof TypeWithClassName) {
                continue;
            }

            if (is_a($unionedType->getClassName(), $class, true)) {
                return true;
            }
        }

        return false;
    }

    private function isArrayWithItemType(Type $propertyType, string $type): bool
    {
        if (! $propertyType instanceof ArrayType) {
            return false;
        }

        $arrayItemType = $propertyType->getItemType();
        if (! $arrayItemType instanceof TypeWithClassName) {
            return false;
        }

        return is_a($arrayItemType->getClassName(), $type, true);
    }
}
