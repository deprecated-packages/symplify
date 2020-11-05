<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class InsideSetup
{
    private const A = 'a';

    public function setUp()
    {
        $this->a = self::A;
    }
}
