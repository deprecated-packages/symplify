<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoFactoryInConstructorRule\Fixture;

final class WithConstructorUseMethodCallFromCurrentObject extends AbstractClass
{
    public function __construct()
    {
        $this->init();
        static::setup();
        self::setup();
        parent::boot();
    }

    protected function init()
    {
    }

    protected static function setup()
    {
    }
}
