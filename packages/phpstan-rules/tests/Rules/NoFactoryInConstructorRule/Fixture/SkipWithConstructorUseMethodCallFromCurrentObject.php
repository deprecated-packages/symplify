<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoFactoryInConstructorRule\Fixture;

final class SkipWithConstructorUseMethodCallFromCurrentObject extends AbstractClass
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
