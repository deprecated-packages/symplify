<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireUniqueEnumConstantRule\Fixture;

use MyCLabs\Enum\Enum;

final class SkipValidEnum extends Enum
{
    public const YES = 'yes';

    public const NO = 'no';
}
