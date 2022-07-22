<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Matcher;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

final class ObjectTypeMatcher
{
    /**
     * @param class-string[] $types
     */
    public function isExprTypes(Expr $expr, Scope $scope, array $types): bool
    {
        $exprType = $scope->getType($expr);

        $exprType = $this->resolveIntersectionedExprType($exprType);
        if (! $exprType instanceof ObjectType) {
            return false;
        }

        foreach ($types as $type) {
            if ($exprType->isInstanceOf($type)->yes()) {
                return true;
            }
        }

        return false;
    }

    private function resolveIntersectionedExprType(Type $type): Type
    {
        if (! $type instanceof IntersectionType) {
            return $type;
        }

        // resolve nested generics to object type
        foreach ($type->getTypes() as $intersectionedType) {
            if ($intersectionedType instanceof ObjectType) {
                return $intersectionedType;
            }
        }

        // fallback
        return $type;
    }
}
