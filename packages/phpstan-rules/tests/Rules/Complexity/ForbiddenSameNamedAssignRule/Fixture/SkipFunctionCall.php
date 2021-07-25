<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipFunctionCall
{
    public function run()
    {
        $values = [1000, 2, 100];

        // re-index
        $values = array_filter($values);

        $values = array_values($values);
    }
}
