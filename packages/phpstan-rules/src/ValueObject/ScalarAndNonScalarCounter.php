<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class ScalarAndNonScalarCounter
{
    public function __construct(
        private int $scalarCount,
        private int $nonScalarCount
    ) {
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
