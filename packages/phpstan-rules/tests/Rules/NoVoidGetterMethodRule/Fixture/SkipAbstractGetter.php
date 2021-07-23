<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoVoidGetterMethodRule\Fixture;

abstract class SkipGetterWithReturn
{
    abstract public function get();
}
