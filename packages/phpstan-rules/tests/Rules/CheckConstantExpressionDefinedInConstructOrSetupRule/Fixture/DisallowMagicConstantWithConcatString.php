<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class DisallowMagicConstantWithConcatString
{
    private const A = 'a';

    public function otherMethod()
    {
        $this->a = __DIR__ . '/static.txt';
    }
}
