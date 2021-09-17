<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\EmbeddedEnumClassConstSpotterRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\EmbeddedEnumClassConstSpotterRule\Source\SomeParentObject;

final class SkipMinMax extends SomeParentObject
{
    public const MIN_TIME = 1;

    public const MIN_PLACE = 2;
}
