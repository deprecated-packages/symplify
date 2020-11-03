<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule\Fixture;

class Factory3
{
    public function build()
    {
    }
}

final class WithConstructorWithFactoryWithMutliAssignment
{
    public function __construct(Factory3 $factory)
    {
        $this->property = $property = $factory->build();
    }
}
