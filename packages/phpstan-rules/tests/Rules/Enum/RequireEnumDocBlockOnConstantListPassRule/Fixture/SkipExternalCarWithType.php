<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\Direction;
use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\ExternalCarWithType;

final class SkipExternalCarWithType
{
    public function goHome(ExternalCarWithType $externalCarWithType)
    {
        $externalCarWithType->turn(Direction::LEFT);
    }
}
