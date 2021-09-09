<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\Source\SomeProduct;

final class SkipDifferentNames
{
    public function run()
    {
        $firstProduct = new SomeProduct();
        $secondProduct = new SomeProduct();
    }
}
