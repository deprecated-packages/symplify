<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNullablePropertyRule\Fixture;

use DateTime;

final class NullableProperty
{
    private ?DateTime $value = null;
}

