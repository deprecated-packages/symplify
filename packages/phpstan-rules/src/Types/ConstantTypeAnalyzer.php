<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Types;

use PHPStan\Type\Constant\ConstantArrayType;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\Type;
use PHPStan\Type\UnionType;

final class ConstantTypeAnalyzer
{
    public function isConstantClassStringType(Type $type, string $classString): bool
    {
        if ($type instanceof ConstantArrayType) {
            return $this->isConstantArrayType($type, $classString);
        }

        if ($type instanceof ConstantStringType) {
            return is_a($type->getValue(), $classString, true);
        }

        return false;
    }

    private function isConstantArrayType(ConstantArrayType $constantArrayType, string $classString): bool
    {
        if ($constantArrayType->getValueTypes() === []) {
            /*
             * If no value types have been derived, it means the array is empty and in that case,
             * technically, the array returns only types of the given class string.
             */
            return true;
        }

        $itemType = $constantArrayType->getItemType();

        if ($itemType instanceof ConstantStringType) {
            return is_a($itemType->getValue(), $classString, true);
        }

        if (! $itemType instanceof UnionType) {
            return false;
        }

        foreach ($itemType->getTypes() as $unionedType) {
            if (! $unionedType instanceof ConstantStringType) {
                return false;
            }

            if (! is_a($unionedType->getValue(), $classString, true)) {
                return false;
            }
        }

        return true;
    }
}
