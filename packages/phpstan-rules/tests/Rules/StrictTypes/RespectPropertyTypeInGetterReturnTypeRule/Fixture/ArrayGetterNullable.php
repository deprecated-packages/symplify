<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule\Fixture;

final class ArrayGetterNullable
{
    private $items = [];

    public function getItems(): array|null
    {
        return $this->items;
    }
}
