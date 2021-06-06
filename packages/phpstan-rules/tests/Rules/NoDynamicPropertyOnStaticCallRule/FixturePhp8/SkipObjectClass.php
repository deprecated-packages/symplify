<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\FixturePhp8;

use stdClass;

final class SkipObjectClass
{
    public function run(stdClass $stdClass): string
    {
        return $stdClass::class;
    }
}
