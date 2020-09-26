<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule\Fixture;

class Factory
{
    public function build()
    {
    }
}

final class WithConstructorWithFactory
{
    public function __construct(Factory $factory)
    {
        $factory->build();
    }
}
