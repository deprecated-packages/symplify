<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class ScalarAndNonScalarCounter
{
    /**
     * @var int
     */
    private $scalarCount;

    /**
     * @var int
     */
    private $nonScalarCount;

    public function __construct(int $scalarCount, int $nonScalarCount)
    {
        $this->scalarCount = $scalarCount;
        $this->nonScalarCount = $nonScalarCount;
    }

    public function getScalarCount(): int
    {
        return $this->scalarCount;
    }

    public function getNonScalarCount(): int
    {
        return $this->nonScalarCount;
    }

    public function getScalarRelative(): float
    {
        $totalCount = $this->getTotalCount();
        if ($totalCount === 0) {
            return 1.0;
        }

        return $this->scalarCount / $totalCount;
    }

    private function getTotalCount(): int
    {
        return $this->nonScalarCount + $this->scalarCount;
    }
}
