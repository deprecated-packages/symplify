<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipInIf
{
    public function run()
    {
        $value = 1000;

        if ('...') {
            $value = 10000;
        }
    }
}
