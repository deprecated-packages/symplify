<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class StringIntConcat
{
    public function otherMethod()
    {
        $value = 1000 . '/static.txt';

        $value2 = '/static.txt' . 1000;
    }
}
