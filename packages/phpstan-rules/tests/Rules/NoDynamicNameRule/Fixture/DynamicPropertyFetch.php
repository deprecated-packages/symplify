<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

final class DynamicPropertyFetchName
{
    public function run($value)
    {
        $this->$value;
    }
}
