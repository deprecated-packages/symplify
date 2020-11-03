<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Source\ClassWithFactory;

final class SkipStaticFactory
{
    public function run()
    {
        return ClassWithFactory::create();
    }
}
