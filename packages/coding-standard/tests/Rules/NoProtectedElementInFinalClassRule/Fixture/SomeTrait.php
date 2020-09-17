<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\NoStaticPropertyRule\Fixture;

trait SomeTrait
{
    abstract protected function run();
    protected $x;
}
