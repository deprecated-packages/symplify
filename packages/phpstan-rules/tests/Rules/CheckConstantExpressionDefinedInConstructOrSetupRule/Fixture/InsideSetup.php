<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

class InsideSetup
{
    private const A = 'a';

    public function setUp()
    {
        $a = self::A;
    }
}
