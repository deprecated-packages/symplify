<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\Direction;

final class SkipWithEnumLikeType
{
    public function goHome()
    {
        $this->turn(Direction::LEFT);
    }

    /**
     * @param Direction::* $direction
     */
    private function turn(string $direction)
    {
    }
}
