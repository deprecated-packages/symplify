<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\Tests\Source;

use Symplify\AutowireArrayParameter\Tests\Source\Contract\CollectedInterface;

final class ArrayShapeCollector
{
    /**
     * @var array<CollectedInterface>
     */
    private $collected = [];

    /**
     * @param array<CollectedInterface> $collected
     */
    public function __construct(array $collected)
    {
        $this->collected = $collected;
    }

    /**
     * @return array<CollectedInterface>
     */
    public function getCollected(): array
    {
        return $this->collected;
    }
}
