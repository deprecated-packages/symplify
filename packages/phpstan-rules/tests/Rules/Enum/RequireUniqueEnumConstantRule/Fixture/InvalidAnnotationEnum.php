<?php

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireUniqueEnumConstantRule\Fixture;

/**
 * @enum
 */
final class InvalidAnnotationEnum
{
    public const YES = 'yes';

    public const NO = 'yes';

    public const MAYBE = 'maybe';
}
