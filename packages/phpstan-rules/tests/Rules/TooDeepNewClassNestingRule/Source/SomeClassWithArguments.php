<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\TooDeepNewClassNestingRule\Source;

final class SomeClassWithArguments
{
    public function __construct($value, $anotherValue, $thirdValue)
    {
    }
}
