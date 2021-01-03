<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\Fixture;

class SkipAllowMagicConstantWithConcatMethodCall
{
    private const A = 'a';

    public function otherMethod()
    {
        $this->a = __DIR__ . $this->getValue();
    }

    private function getValue()
    {
        return '';
    }
}
