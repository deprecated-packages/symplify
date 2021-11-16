<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\ForbiddenClassConstRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\ForbiddenClassConstRule\Source\AbstractEntity;

final class SkipMinMaxConstant extends AbstractEntity
{
    public const MIN_TIME = 1;

    public const MIN_PLACE = 2;
}
