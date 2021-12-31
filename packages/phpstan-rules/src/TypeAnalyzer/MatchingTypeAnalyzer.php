<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use PHPStan\Type\ErrorType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

final class MatchingTypeAnalyzer
{
    /**
     * @param Type[] $expectedTypes
     */
    public function isTypeMatchingExpectedTypes(Type $argType, array $expectedTypes): bool
    {
        foreach ($expectedTypes as $expectedType) {
            if ($this->isUnionTypeMatch($argType, $expectedType)) {
                return true;
            }

            // special case for __toString() object
            if ($this->isMatchingToStringObjectType($argType, $expectedType)) {
                return true;
            }

            if ($expectedType->isSuperTypeOf($argType)->yes()) {
                return true;
            }

            // handle "$%s"
            if ($argType instanceof ErrorType) {
                return true;
            }
        }

        return false;
    }

    private function isMatchingToStringObjectType(Type $argType, Type $expectedType): bool
    {
        if (! $expectedType instanceof StringType) {
            return false;
        }

        if (! $argType instanceof ObjectType) {
            return false;
        }

        return $argType->hasMethod('__toString')
            ->yes();
    }

    private function isUnionTypeMatch(Type $argType, Type $expectedType): bool
    {
        if (! $argType instanceof UnionType) {
            return false;
        }

        foreach ($argType->getTypes() as $unionedArgType) {
            if ($expectedType->isSuperTypeOf($unionedArgType)->yes()) {
                return true;
            }

            if ($this->isMatchingToStringObjectType($unionedArgType, $expectedType)) {
                return true;
            }
        }

        return false;
    }
}
