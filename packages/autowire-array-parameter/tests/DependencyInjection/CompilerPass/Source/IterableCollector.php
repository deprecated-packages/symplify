<?php

declare(strict_types=1);

namespace Symplify\AutowireArrayParameter\Tests\DependencyInjection\CompilerPass\Source;

use Symplify\AutowireArrayParameter\Tests\DependencyInjection\CompilerPass\Source\Contract\FirstCollectedInterface;
use Symplify\AutowireArrayParameter\Tests\DependencyInjection\CompilerPass\Source\Contract\SecondCollectedInterface;

final class IterableCollector
{
    /**
     * @var iterable<FirstCollectedInterface>
     */
    private $firstCollected = [];

    /**
     * @var iterable<SecondCollectedInterface>
     */
    private $secondCollected = [];

    /**
     * @param iterable<FirstCollectedInterface> $firstCollected
     * @param iterable<SecondCollectedInterface> $secondCollected
     */
    public function __construct(array $firstCollected, array $secondCollected)
    {
        $this->firstCollected = $firstCollected;
        $this->secondCollected = $secondCollected;
    }

    /**
     * @return iterable<FirstCollectedInterface>
     */
    public function getFirstCollected(): array
    {
        return $this->firstCollected;
    }

    /**
     * @return iterable<SecondCollectedInterface>
     */
    public function getSecondCollected(): array
    {
        return $this->secondCollected;
    }
}
