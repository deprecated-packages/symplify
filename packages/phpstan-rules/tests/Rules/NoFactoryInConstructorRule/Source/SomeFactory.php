<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Source;

final class SomeFactory
{
    public function create()
    {
        return 123;
    }
}
