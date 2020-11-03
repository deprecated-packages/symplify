<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDefaultParameterValueRule\Fixture;

final class MethodWithDefaultParamValue
{
    public function run($value = true): void
    {
    }
}
