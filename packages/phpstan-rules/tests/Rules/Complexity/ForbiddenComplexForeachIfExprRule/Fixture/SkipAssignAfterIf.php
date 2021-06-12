<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenComplexForeachIfExprRule\Fixture;

final class SkipAssignAfterIf
{
    public function run()
    {
        function data()
        {
            return rand(1, 2);
        }

        $a = data();
        if ($a === 1) {
            $a = 2;
        }
    }
}
