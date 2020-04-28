<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\CognitiveComplexity\DataCollector;

final class CognitiveComplexityDataCollector
{
    /**
     * @var int
     */
    private $cognitiveComplexity = 0;

    public function increase(int $steps): void
    {
        $this->cognitiveComplexity += $steps;
    }

    public function getCognitiveComplexity(): int
    {
        return $this->cognitiveComplexity;
    }

    public function reset(): void
    {
        $this->cognitiveComplexity = 0;
    }
}
