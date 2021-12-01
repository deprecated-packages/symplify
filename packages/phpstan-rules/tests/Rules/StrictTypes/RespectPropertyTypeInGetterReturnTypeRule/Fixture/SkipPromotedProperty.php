<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\StrictTypes\RespectPropertyTypeInGetterReturnTypeRule\Fixture;

final class SkipPromotedProperty
{
    public function __construct(
        private string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
