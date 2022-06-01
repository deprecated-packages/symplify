<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\Direction;
use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\ExternalCarWithoutType;

final class SkipClassConstReference
{
    public function goHome(ExternalCarWithoutType $externalCarWithType)
    {
        $externalCarWithType->turn(Direction::class);
    }
}
