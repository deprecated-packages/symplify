<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class InsideOtherMethod
{
    private const A = 'a';

    public function otherMethod()
    {
        $this->a = self::A;
    }
}
