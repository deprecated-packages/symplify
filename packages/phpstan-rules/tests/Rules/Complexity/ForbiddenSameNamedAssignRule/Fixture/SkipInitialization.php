<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

final class SkipInitialization
{
    public function run()
    {
        $files = [];
        if (100) {
            $files = ['file'];
        }
    }
}
