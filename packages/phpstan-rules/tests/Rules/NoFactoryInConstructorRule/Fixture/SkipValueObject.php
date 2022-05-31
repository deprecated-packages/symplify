<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Source\ValueObject\ValueObject;

final class SkipValueObject
{
    private int $value;

    public function __construct(ValueObject $valueObject)
    {
        $this->value = $valueObject->getNumber();
    }
}
