<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\ForbiddenClassConstRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\ForbiddenClassConstRule\Source\AbstractEntity;

final class SkipValidClassConstant extends AbstractEntity
{
    public $name;
}
