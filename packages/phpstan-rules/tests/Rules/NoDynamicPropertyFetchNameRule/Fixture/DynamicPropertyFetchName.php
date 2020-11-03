<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyFetchNameRule\Fixture;

final class DynamicPropertyFetchName
{
    public function run($value)
    {
        $this->$value;
    }
}
