<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Domain\ForbiddenAlwaysSetterCallRule\Source;

final class FirstClassIdea
{
    public function __construct(?int $motivation = null)
    {
    }

    public function addMotivation($motivation)
    {
    }
}
