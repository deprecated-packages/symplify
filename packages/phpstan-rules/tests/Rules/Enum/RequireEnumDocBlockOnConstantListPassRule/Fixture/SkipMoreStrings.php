<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Fixture;

use PhpParser\Node\Scalar\MagicConst\Dir;
use Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source\Direction;

final class SkipMoreStrings
{
    public function goHome()
    {
        $this->turn(Direction::LEFT, 'three');
    }

    /**
     * @param Direction::* $direction
     */
    private function turn(string $direction, string $gear)
    {
    }
}
