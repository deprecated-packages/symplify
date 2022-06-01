<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\Direction;
use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\ExternalCarWithoutType;

final class ExternalNoType
{
    public function goHome(ExternalCarWithoutType $externalCarWithoutType)
    {
        // some comment
        $externalCarWithoutType->turn(Direction::LEFT);
    }
}
