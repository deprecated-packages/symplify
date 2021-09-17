<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\EmbeddedEnumClassConstSpotterRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\EmbeddedEnumClassConstSpotterRule\Source\SomeParentObject;

final class SkipMinMaxMultiple extends SomeParentObject
{
    public const MIN_TITLE_LENGTH = 3;

    public const MAX_PASSWORD_LENGTH = 30;
}
