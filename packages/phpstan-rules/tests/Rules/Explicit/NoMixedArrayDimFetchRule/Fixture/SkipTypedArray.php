<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\NoMixedArrayDimFetchRule\Fixture;

final class SkipTypedArray
{
    /**
     * @var array<string, string>
     */
    private $items = [];

    public function addItem(string $key, string $value)
    {
        $this->items[$key] = $value;
    }
}
