<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule\Fixture;

final class SkipNullableSetter
{
    private $items = [];

    public function setItems(array|null $items): void
    {
        $this->items = $items;
    }

    public function getItems(): array|null
    {
        return $this->items;
    }
}
