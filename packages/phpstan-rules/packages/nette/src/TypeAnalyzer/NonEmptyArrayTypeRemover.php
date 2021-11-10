<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Nette\TypeAnalyzer;

use PHPStan\Type\Accessory\NonEmptyArrayType;
use PHPStan\Type\IntersectionType;
use PHPStan\Type\Type;

final class NonEmptyArrayTypeRemover
{
    public function clean(Type $type): Type
    {
        if (! $type instanceof IntersectionType) {
            return $type;
        }

        $filteredTypes = [];
        foreach ($type->getTypes() as $intersectedType) {
            if ($intersectedType instanceof NonEmptyArrayType) {
                continue;
            }

            $filteredTypes[] = $intersectedType;
        }

        if (count($filteredTypes) === 1) {
            return $filteredTypes[0];
        }

        if (count($filteredTypes) > 1) {
            new IntersectionType($filteredTypes);
        }

        return $type;
    }
}
