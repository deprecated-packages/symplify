<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicNameRule\Fixture;

final class DynamicConstantName
{
    public function run($value)
    {
        $value::SOMETHING;
    }
}
