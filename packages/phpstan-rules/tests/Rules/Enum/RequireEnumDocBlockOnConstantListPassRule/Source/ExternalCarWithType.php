<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Enum\RequireEnumDocBlockOnConstantListPassRule\Source;

final class ExternalCarWithType
{
    /**
     * @param Direction::* $direction
     */
    public function turn(string $direction)
    {
    }
}
