<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\Direction;

final class OnePositionCovered
{
    public function goHome()
    {
        $this->turn(Direction::LEFT, Direction::RIGHT);
    }

    /**
     * @param Direction::* $direction
     */
    private function turn(string $direction, string $otherDirection)
    {
    }
}
