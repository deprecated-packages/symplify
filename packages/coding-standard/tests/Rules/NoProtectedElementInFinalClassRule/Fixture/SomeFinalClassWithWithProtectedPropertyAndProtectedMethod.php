<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule\Fixture;

final class SomeFinalClassWithWithProtectedPropertyAndProtectedMethod
{
    protected $x = [];

    protected function run()
    {
    }
}
