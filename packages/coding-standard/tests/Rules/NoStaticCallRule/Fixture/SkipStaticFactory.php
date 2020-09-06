<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Source\ClassWithStaticFactory;

final class SkipStaticFactory
{
    public function run()
    {
        return ClassWithStaticFactory::create();
    }
}
