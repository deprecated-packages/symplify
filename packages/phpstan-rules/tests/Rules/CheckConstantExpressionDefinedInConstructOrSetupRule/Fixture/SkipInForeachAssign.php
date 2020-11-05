<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class SkipInForeachAssign
{
    public function run()
    {
        for ($i = 1; $i <= 100; $i++) {
            continue;
        }
    }
}
