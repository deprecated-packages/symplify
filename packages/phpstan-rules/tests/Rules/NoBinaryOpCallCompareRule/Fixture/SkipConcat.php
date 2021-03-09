<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoBinaryOpCallCompareRule\Fixture;

final class SkipConcat
{
    public function run($value)
    {
        $value = $value . '...';
    }
}
