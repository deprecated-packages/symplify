<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\ExternalCarWithoutType;

final class SkipSelfReference
{
    public const DIRECTION_LEFT = 'left';

    public function goHome(ExternalCarWithoutType $externalCarWithType)
    {
        $externalCarWithType->turn(self::DIRECTION_LEFT);
    }
}
