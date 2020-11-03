<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\CheckConstantStringValueFormatRule\Fixture;

class Invalid
{
    private const FOO = 'ok$';
}
