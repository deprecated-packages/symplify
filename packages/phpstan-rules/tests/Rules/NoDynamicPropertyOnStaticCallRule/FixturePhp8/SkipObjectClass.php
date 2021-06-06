<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\FixturePhp8;

use stdClass;

final class SkipObjectClass
{
    private stdClass $foo;

    public function run(): string
    {
        return $this->foo::class;
    }
}
