<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoModifyAndReturnSelfObjectRule\Fixture;

use PHPStan\Type\NullType;
use PHPStan\Type\StringType;
use PHPStan\Type\UnionType;

final class SkipAnotherType
{
    public function run(\PHPStan\Type\Type $type)
    {
        if (! $type instanceof UnionType){
            return $type;
        }

        $unionedTypes = $type->getTypes();
        if (count($unionedTypes) !== 2) {
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
