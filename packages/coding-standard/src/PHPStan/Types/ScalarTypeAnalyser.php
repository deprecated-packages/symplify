<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\PHPStan\Types;

use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\NullType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

final class ScalarTypeAnalyser
{
    public function isScalarOrArrayType(Type $type): bool
    {
        if ($type instanceof StringType) {
            return true;
        }

        if ($type instanceof FloatType) {
            return true;
        }
        if ($type instanceof BooleanType) {
            return true;
        }

        if ($type instanceof IntegerType) {
            return true;
        }

        if ($type instanceof ArrayType) {
            return $this->isScalarOrArrayType($type->getItemType());
        }
        return $this->isNullableScalarType($type);
    }

    private function isNullableScalarType(Type $type): bool
    {
        if (! $type instanceof UnionType) {
            return false;
        }

        if (count($type->getTypes()) !== 2) {
            return false;
        }

        if (! $type->isSuperTypeOf(new NullType())->yes()) {
            return false;
        }

        foreach ($type->getTypes() as $unionedType) {
            if ($this->isScalarOrArrayType($unionedType)) {
                return true;
            }
        }

        return false;
    }
}
