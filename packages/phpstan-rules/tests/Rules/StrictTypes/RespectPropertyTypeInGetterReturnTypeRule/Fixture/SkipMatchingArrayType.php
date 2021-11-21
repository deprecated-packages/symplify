<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule\Fixture;

final class SkipMatchingArrayType
{
    private $items = [];

    public function getItems(): array
    {
        return $this->items;
    }
}
