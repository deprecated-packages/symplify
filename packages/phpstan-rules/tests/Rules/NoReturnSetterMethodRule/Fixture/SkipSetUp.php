<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoReturnSetterMethodRule\Fixture;

final class SkipSetUp
{
    protected function setUp()
    {
        return 100;
    }
}
