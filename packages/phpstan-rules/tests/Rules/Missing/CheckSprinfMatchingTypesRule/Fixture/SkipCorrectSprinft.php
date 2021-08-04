<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprinfMatchingTypesRule\Fixture;

final class SkipCorrectSprinft
{
    public function run()
    {
        return sprintf('My name is %s and I have %d children', 'Tomas', 10);
    }
}
