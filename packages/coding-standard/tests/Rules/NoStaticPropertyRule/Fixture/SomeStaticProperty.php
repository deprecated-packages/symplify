<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule\Fixture;

final class SomeStaticProperty
{
    protected static $customFileNames = [];
    protected static $customFileNames2 = [];

    public function run()
    {
        return self::$customFileNames;
    }

    public function again()
    {
        return self::$customFileNames2;
    }
}
