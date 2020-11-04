<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

class DisallowMagicConstantWithConcatString
{
    private const A = 'a';

    public function otherMethod()
    {
        $a = __DIR__ . '/static.txt';
    }
}
