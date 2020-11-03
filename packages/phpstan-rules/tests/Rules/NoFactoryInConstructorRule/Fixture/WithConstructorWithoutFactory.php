<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule\Fixture;

class NotFactory
{
}

final class WithConstructorWithoutFactory
{
    public function __construct(NotFactory $notFactory)
    {
        $this->property = $notFactory;
    }
}
