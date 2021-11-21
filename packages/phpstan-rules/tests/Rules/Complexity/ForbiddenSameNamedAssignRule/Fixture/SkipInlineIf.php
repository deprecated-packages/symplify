<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipInlineIf
{

    public function run()
    {
        $block = ($uid = 1000) || ($uid = 1000);
    }
}
