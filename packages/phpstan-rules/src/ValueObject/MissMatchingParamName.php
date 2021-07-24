<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ValueObject;

final class MissMatchingParamName
{
    public function __construct(
        private int $position,
        private string $currentName,
        private string $parentName
    ) {
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getCurrentName(): string
    {
        return $this->currentName;
    }

    public function getParentName(): string
    {
        return $this->parentName;
    }
}
