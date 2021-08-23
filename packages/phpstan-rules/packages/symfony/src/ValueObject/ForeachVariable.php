<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Symfony\ValueObject;

final class ForeachVariable
{
    public function __construct(
        private string $arrayName,
        private string $itemName
    ) {
    }

    public function getArrayName(): string
    {
        return $this->arrayName;
    }

    public function getItemName(): string
    {
        return $this->itemName;
    }
}
