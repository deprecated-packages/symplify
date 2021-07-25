<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedAssignRule\Fixture;

use PHPUnit\Framework\TestCase;

final class SkipTestCase extends TestCase
{
    public function something()
    {
        $testMessage = 'compare';

        $testMessage = 'compare too';
    }
}
