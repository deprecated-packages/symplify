<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule\Fixture;

final class SkipEventSubscriber
{
    public static function run(): void
    {
    }
}
