<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class DisallowMagicConstantWithConcatString
{
    public function otherMethod()
    {
        $variable = __DIR__ . '/static.txt';
    }
}
