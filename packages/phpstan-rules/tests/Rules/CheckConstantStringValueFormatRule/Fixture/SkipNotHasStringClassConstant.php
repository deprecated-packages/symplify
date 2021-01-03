<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantStringValueFormatRule\Fixture;

class SkipNotHasStringClassConstant
{
    private const FOO = 1;
}
