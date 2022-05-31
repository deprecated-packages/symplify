<?php

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Source\ValueObject;

final class ValueObject
{
    private int $number;

    public function __construct(int $number)
    {
        $this->number = $number;
    }

    public function getNumber(): int
    {
        return $this->number;
    }
}
