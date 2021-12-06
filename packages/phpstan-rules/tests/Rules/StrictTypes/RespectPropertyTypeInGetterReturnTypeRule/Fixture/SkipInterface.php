<?php

namespace Symplify\PHPStanRules\Tests\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule\Fixture;

interface SkipInterface
{
    public function getItems(): array|null;
}
