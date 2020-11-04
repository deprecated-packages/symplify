<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantStringValueFormatRule\Fixture;

class HasArrayClassContant
{
    private const SOME_VALUE = ['One', 'Two', 'Three'];
}
