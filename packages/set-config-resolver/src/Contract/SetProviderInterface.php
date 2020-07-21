<?php

declare(strict_types=1);

namespace Symplify\SetConfigResolver\Contract;

use Symplify\SetConfigResolver\ValueObject\Set;

interface SetProviderInterface
{
    /**
     * @return Set[]
     */
    public function provide(): array;

    /**
     * @return string[]
     */
    public function provideSetNames(): array;

    public function provideByName(string $setName): ?Set;
}
