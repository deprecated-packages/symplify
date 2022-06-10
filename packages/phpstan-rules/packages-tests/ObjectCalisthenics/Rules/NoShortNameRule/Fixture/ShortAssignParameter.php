<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\ObjectCalisthenics\Rules\NoShortNameRule\Fixture;

final class ShortAssignParameter
{
    public function run()
    {
        $n = 1000000000;

        ++$n;

        return $n;
    }
}
