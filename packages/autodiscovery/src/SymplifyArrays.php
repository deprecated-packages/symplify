<?php

declare(strict_types=1);

namespace Symplify\Autodiscovery;

final class SymplifyArrays
{
    /**
     * @param mixed[] $items
     */
    public function hasOnlyKey(array $items, string $key): bool
    {
        if (count($items) !== 1) {
            return false;
        }

        return isset($items[$key]);
    }
}
