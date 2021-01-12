<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\Tests\Source;

use Symplify\AutowireArrayParameter\Tests\Source\Contract\CollectedInterface;

final class IterableCollector
{
    /**
     * @var iterable<CollectedInterface>
     */
    private $collected = [];

    /**
     * @param iterable<CollectedInterface> $collected
     */
    public function __construct(array $collected)
    {
        $this->collected = $collected;
    }

    /**
     * @return iterable<CollectedInterface>
     */
    public function getCollected(): array
    {
        return $this->collected;
    }
}
