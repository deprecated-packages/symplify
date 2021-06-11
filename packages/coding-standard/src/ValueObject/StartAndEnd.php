<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ValueObject;

final class StartAndEnd
{
    public function __construct(
        private int $start,
        private int $end
    ) {
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }
}
