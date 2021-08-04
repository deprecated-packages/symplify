<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Missing\CheckSprinfMatchingTypesRule\Fixture;

final class MissMatchSprinft
{
    public function run()
    {
        return sprintf('My name is %s and I have %d children', 10, 'Tomas');
    }
}
