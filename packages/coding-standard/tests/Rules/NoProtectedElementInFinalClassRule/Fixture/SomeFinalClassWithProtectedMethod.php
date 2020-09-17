<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoProtectedElementInFinalClassRule\Fixture;

final class SomeFinalClassWithProtectedMethod
{
    protected function run()
    {
    }
}
