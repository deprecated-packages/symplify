<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

final class DynamicPropertyFetch
{
    public function run($value)
    {
        $this->$value;
    }
}
