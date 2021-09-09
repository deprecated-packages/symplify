<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\Fixture;

final class SkipNonObjectAssigns
{
    public function run()
    {
        $value = 1000;
        $value = 10000;
    }
}
