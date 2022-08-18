<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\Tests\DependencyInjection\CompilerPass\Source;

use Symplify\AutowireArrayParameter\Tests\DependencyInjection\CompilerPass\Source\Contract\FirstCollectedInterface;

final class PromotedPropertyCollector
{
    /**
     * @param FirstCollectedInterface[] $firstCollected
     */
    public function __construct(
        private array $firstCollected
    ) {
    }

    /**
     * @return FirstCollectedInterface[]
     */
    public function getFirstCollected(): array
    {
        return $this->firstCollected;
    }
}
