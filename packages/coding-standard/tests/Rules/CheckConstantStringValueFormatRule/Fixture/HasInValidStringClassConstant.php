<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckConstantStringValueFormatRule\Fixture;

class HasInValidStringClassConstant
{
    private const FOO = 'ok$';
}
