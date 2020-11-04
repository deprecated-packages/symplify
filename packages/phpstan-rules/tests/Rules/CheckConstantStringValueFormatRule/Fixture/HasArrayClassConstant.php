<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantStringValueFormatRule\Fixture;

class HasArrayClassConstant
{
    private const SOME_VALUE = ['One', 'Two', 'Three'];
}
