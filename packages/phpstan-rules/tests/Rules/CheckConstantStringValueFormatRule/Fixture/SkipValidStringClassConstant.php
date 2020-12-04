<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantStringValueFormatRule\Fixture;

class SkipValidStringClassConstant
{
    private const ERROR_MESSAGE = 'this is a';
    private const A_REGEX = '';
}
