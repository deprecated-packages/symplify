<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoIssetOnObjectRule\Fixture;

final class SkipIssetOnArray
{
    public function run(array $values)
    {
        if (isset($values[9])) {
            return $values[9];
        }
    }
}
