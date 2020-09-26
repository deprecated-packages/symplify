<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule\Fixture;

final class WithConstructorUseMethodCallFromCurrentObject
{
    public function __construct()
    {
        $this->init();
        static::setup();
        self::setup();
    }

    protected function init()
    {
    }

    protected static function setup()
    {
    }
}
