<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedArrayDimFetchRule\Fixture;

final class SkipString
{
    public function addItem(string $someString, int $key, string $value)
    {
        $someString[$key] = $value;
    }
}
