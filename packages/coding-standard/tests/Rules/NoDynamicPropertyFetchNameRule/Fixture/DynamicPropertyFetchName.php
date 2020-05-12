<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoDynamicPropertyFetchNameRule\Fixture;

final class DynamicPropertyFetchName
{
    public function run($value)
    {
        $this->$value;
    }
}
