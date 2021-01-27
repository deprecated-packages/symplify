<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDynamicPropertyOnStaticCallRule\Fixture;

final class SkipSelfStatic
{
    public function run()
    {
        self::literal;
        self::literal();
        static::literal;
        static::literal();
    }
}
