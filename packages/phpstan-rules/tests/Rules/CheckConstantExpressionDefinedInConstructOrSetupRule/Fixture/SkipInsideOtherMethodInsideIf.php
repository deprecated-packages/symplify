<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class SkipInsideOtherMethodInsideIf
{
    public function otherMethodInsideIf($a)
    {
        if (true) {
            $a = 'very static string';
        }

        return $a;
    }
}
