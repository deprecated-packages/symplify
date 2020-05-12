<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\DataCollector;

final class CognitiveComplexityDataCollector
{
    /**
     * @var int
     */
    private $operationComplexity = 0;

    /**
     * @var int
     */
    private $nestingComplexity = 0;

    public function increaseOperation(): void
    {
        ++$this->operationComplexity;
    }

    public function increaseNesting(int $steps): void
    {
        $this->nestingComplexity += $steps;
    }

    public function getCognitiveComplexity(): int
    {
        return $this->nestingComplexity + $this->operationComplexity;
    }

    public function reset(): void
    {
        $this->operationComplexity = 0;
        $this->nestingComplexity = 0;
    }
}
