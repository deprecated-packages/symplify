<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\PreventDuplicateClassMethodRule\Fixture;

class Entity2
{
    public function setX(string $x)
    {
        $this->x = $x;
    }
}
