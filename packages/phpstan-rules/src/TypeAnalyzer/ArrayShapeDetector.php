<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use PhpParser\Node\Expr;
use PHPStan\Analyser\Scope;
use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantIntegerType;
use PHPStan\Type\NeverType;
use PHPStan\Type\UnionType;

final class ArrayShapeDetector
{
    public function isTypeArrayShapeCandidate(Expr $expr, Scope $scope): bool
    {
        $exprType = $scope->getType($expr);
        if (! $exprType instanceof ConstantArrayType) {
            return false;
        }

        // empty array
        if ($exprType->getIterableKeyType() instanceof NeverType) {
            return false;
        }

        return ! $this->hasConstantIntegerKeys($exprType);
    }

    private function hasConstantIntegerKeys(ConstantArrayType $constantArrayType): bool
    {
        if ($constantArrayType->getKeyType() instanceof ConstantIntegerType) {
            return true;
        }

        if ($constantArrayType->getKeyType() instanceof UnionType) {
            $keyType = $constantArrayType->getKeyType();
            foreach ($keyType->getTypes() as $unionedType) {
                if (! $unionedType instanceof ConstantIntegerType) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }
}
