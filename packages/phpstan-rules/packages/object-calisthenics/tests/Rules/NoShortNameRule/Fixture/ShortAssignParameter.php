<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Tests\Rules\NoShortNameRule\Fixture;

final class ShortAssignParameter
{
    public function run()
    {
        $n = 1000000000;

        ++$n;

        return $n;
    }
}
