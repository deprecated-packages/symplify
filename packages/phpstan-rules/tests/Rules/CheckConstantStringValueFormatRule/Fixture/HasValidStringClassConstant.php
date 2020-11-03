<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckConstantStringValueFormatRule\Fixture;

class HasValidStringClassConstant
{
    private const FOO = 'ok';
    private const ERROR_MESSAGE = '';
    private const A_REGEX = '';
}
