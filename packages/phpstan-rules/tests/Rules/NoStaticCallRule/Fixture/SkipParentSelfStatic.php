<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Fixture;

use Symplify\CodingStandard\Tests\Rules\NoStaticCallRule\Source\SomeParentClass;

class SkipParentSelfStatic extends SomeParentClass
{
    public function __construct()
    {
        parent::__construct();
    }

    public function run()
    {
        return self::assert();
    }

    public function assert()
    {
    }
}
