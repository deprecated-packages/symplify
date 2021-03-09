<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoBinaryOpCallCompareRule\Fixture;

final class SkipFuncCallCount
{
    public function run($values)
    {
        if (count($values) === 2) {
            return true;
        }

        return false;
    }
}
