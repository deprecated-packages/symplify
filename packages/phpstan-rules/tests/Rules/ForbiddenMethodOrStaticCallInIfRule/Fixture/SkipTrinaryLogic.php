<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInIfRule\Fixture;

use PHPStan\Type\NullType;

final class SkipTrinaryLogic
{
    public function run(\PHPStan\Type\Type $type)
    {
        if ($type->isSuperTypeOf(new NullType())->yes()) {
            return false;
        }

        return false;
    }
}
