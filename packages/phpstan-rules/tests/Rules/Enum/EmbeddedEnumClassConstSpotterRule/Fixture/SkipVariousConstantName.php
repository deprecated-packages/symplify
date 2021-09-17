<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\EmbeddedEnumClassConstSpotterRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\EmbeddedEnumClassConstSpotterRule\Source\SomeParentObject;

final class SkipVariousConstantName extends SomeParentObject
{
    public const YES = 1;

    public const NO = 2;
}
