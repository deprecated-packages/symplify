<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDynamicMethodNameRule\Fixture;

final class DynamicMethodCallName
{
    public function run($value)
    {
        $this->$value();
    }
}
