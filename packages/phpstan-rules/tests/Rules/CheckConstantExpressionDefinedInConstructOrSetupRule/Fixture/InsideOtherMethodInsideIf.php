<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

class InsideOtherMethodInsideIf
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
