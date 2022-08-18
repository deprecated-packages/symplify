<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Fixture;

use stdClass;

final class SkipObjectClass
{
    private stdClass $foo;

    public function run(): string
    {
        return $this->foo::class;
    }
}
