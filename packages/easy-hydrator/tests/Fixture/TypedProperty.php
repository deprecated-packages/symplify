<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Tests\Fixture;

final class TypedProperty
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
