<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\Source\SomeProduct;

final class SkipNullDefaultAssign
{
    public function run()
    {
        $item = null;
        if (mt_rand(0, 1)) {
            $item = new SomeProduct();
        }

        return $item;
    }
}
