<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprintfMatchingTypesRule\Fixture;

final class SkipCorrectSprintf
{
    public function run()
    {
        return sprintf('My name is %s and I have %d children', 'Tomas', 10);
    }
}
