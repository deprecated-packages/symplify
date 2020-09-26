<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule\Fixture;

class Factory2
{
    public function build()
    {
    }
}

final class WithConstructorWithFactoryWithAssignment
{
    public function __construct(Factory2 $factory)
    {
        $this->property = $factory->build();
    }
}
