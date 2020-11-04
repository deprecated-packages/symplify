<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

class AllowInsideOtherMethodUsedAfterDefinition
{
    private const A = 'a';

    public function otherMethod()
    {
        $a = self::A;

        assert(is_string($a));
    }
}
