<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipUserDefinedVariables
{
    public function run()
    {
        $params = [1, 2, 3];

        $params = [2, 3];
    }
}
