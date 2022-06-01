<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\Direction;

final class ClassWithoutParamEnumType
{
    public function goHome()
    {
        $this->turn(Direction::LEFT);
    }

    private function turn(string $direction)
    {
    }
}
