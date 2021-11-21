<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule\Fixture;

final class IntegerGetterFloat
{
    private $items = 100;

    public function getItems(): float
    {
        return $this->items;
    }
}
