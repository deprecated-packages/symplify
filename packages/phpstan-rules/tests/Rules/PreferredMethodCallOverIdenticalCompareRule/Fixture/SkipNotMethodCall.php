<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreferredMethodCallOverIdenticalCompareRule\Fixture;

final class SkipNotMethodCall
{
    public function run()
    {
        $a === $b;
    }
}
