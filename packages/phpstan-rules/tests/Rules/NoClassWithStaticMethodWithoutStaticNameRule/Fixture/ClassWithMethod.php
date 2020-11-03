<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoClassWithStaticMethodWithoutStaticNameRule\Fixture;

final class ClassWithMethod
{
    public static function run(): void
    {
    }
}
