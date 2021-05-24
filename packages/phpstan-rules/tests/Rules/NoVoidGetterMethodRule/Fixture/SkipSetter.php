<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoVoidGetterMethodRule\Fixture;

final class SkipSetter
{
    public function set(): void
    {
    }
}
