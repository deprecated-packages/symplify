<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class SkipInsideOtherMethodInsideIf
{
    private const A = 'a';

    public function otherMethodInsideIf($a)
    {
        if (true) {
            $a = self::A;
        }

        return $a;
    }
}
