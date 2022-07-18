<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Matcher;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\ObjectType;

final class ObjectTypeMatcher
{
    /**
     * @param class-string[] $types
     */
    public function isExprTypes(Expr $expr, Scope $scope, array $types): bool
    {
        $exprType = $scope->getType($expr);

        if ($exprType instanceof IntersectionType) {
            // resolve nested generics to object type
            foreach ($exprType->getTypes() as $intersectionedType) {
                if ($intersectionedType instanceof ObjectType) {
                    $exprType = $intersectionedType;
                    break;
                }
            }
        }

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
}
