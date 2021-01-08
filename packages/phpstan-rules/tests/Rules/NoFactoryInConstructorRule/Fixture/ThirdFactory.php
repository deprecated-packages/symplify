<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Fixture;

final class ThirdFactory
{
    public function build()
    {
        return 1000;
    }
}
