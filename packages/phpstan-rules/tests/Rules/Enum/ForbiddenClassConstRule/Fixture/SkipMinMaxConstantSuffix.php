<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\ForbiddenClassConstRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\ForbiddenClassConstRule\Source\AbstractEntity;

final class SkipMinMaxConstantSuffix extends AbstractEntity
{
    public const TIME_MIN = 1;

    public const PLACE_MIN = 2;

    public const TIME_MAX = 3;

    public const PLACE_MAX = 4;
}
