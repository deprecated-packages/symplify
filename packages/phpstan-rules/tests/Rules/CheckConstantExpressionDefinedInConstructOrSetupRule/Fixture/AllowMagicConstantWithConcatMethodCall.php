<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckParentChildMethodParameterTypeCompatibleRule\Fixture;

class AllowMagicConstantWithConcatMethodCall
{
    private const A = 'a';

    public function otherMethod()
    {
        $a = __DIR__ . $this->getValue();
    }

    private function getValue()
    {
        return '';
    }
}
