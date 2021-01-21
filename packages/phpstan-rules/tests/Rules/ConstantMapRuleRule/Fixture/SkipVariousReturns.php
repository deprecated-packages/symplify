<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ConstantMapRuleRule\Fixture;

final class SkipVariousReturns
{
    public function run()
    {
        if (random_int(0, 100)) {
            return 1;
        }

        return 2;
    }
}
