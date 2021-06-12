<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ObjectCalisthenics\Marker;

final class IndentationMarker
{
    private int $indentation = 0;

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
