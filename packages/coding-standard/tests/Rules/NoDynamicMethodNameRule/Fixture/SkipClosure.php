<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDynamicMethodNameRule\Fixture;

use Closure;

final class SkipClosure
{
    public function run(Closure $value)
    {
        $value();
    }
}
