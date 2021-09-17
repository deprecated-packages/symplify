<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\EmbeddedEnumClassConstSpotterRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\EmbeddedEnumClassConstSpotterRule\Source\SomeParentObject;

final class ClassWithEnums extends SomeParentObject
{
    public const TYPE_ACTIVE = 1;

    public const TYPE_PASSIVE = 2;
}
