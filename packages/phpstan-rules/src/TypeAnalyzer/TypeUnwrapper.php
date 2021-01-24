<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use PHPStan\Type\NullType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

final class TypeUnwrapper
{
    public function unwrapNullableType(Type $type): Type
    {
        if (! $type instanceof UnionType) {
            return $type;
        }

        $unionedTypes = $type->getTypes();
        if (count($unionedTypes) !== 2) {
            return $type;
        }

        $nullSuperTypeTrinaryLogic = $type->isSuperTypeOf(new NullType());
        if (! $nullSuperTypeTrinaryLogic->yes()) {
            return $type;
        }

        foreach ($unionedTypes as $unionedType) {
            if ($unionedType instanceof NullType) {
                continue;
            }

            return $unionedType;
        }

        return $type;
    }
}
