<?php


namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireUniqueEnumConstantRule\Fixture;

use MyCLabs\Enum\Enum;

final class InvalidEnum extends Enum
{
    public const YES = 'yes';

    public const NO = 'yes';

    public const MAYBE = 'maybe';
}
