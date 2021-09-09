<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\Source\SomeProduct;

final class SameObjectAssigns
{
    public function run()
    {
        $someProduct = new SomeProduct();
        $someProduct = new SomeProduct();
    }
}
