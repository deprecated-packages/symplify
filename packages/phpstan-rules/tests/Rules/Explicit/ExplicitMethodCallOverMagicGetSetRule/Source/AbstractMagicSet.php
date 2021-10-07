<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Source;

abstract class AbstractMagicSet
{
    public function __set($name, $value)
    {
    }
}
