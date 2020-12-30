<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoNullableArrayPropertyRule\FixturePhp74;

use DateTime;

final class SkipClassNameProperty
{
    private ?DateTime $value;
}

