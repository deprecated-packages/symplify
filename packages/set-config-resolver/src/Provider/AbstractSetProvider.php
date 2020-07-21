<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Provider;

use Symplify\SetConfigResolver\Contract\SetProviderInterface;
use Symplify\SetConfigResolver\ValueObject\Set;

abstract class AbstractSetProvider implements SetProviderInterface
{
    /**
     * @return string[]
     */
    public function provideSetNames(): array
    {
        $setNames = [];
        foreach ($this->provide() as $set) {
            $setNames[] = $set->getName();
        }

        return $setNames;
    }

    public function provideByName(string $setName): ?Set
    {
        foreach ($this->provide() as $set) {
            if ($set->getName() !== $setName) {
                continue;
            }

            return $set;
        }

        return null;
    }
}
