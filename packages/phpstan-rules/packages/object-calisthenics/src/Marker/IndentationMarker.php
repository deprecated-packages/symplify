<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\ObjectCalisthenics\Marker;

final class IndentationMarker
{
    /**
     * @var int
     */
    private $indentation = 0;

    public function reset(): void
    {
        $this->indentation = 0;
    }

    public function markIndentation(int $indentation): void
    {
        $this->indentation = max($indentation, $this->indentation);
    }

    public function getIndentation(): int
    {
        return $this->indentation;
    }
}
