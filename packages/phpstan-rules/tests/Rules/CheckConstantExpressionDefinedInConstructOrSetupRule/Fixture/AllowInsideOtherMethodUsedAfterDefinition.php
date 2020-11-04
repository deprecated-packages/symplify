<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

class AllowInsideOtherMethodUsedAfterDefinition
{
    public function otherMethod()
    {
        $a = __DIR__ . '/static.txt';

        assert(is_string($a));
    }
}
