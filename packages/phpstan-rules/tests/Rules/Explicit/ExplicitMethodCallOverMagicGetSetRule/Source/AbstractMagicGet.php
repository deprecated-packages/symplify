<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\ExplicitMethodCallOverMagicGetSetRule\Source;

abstract class AbstractMagicGet
{
    public function __get($name)
    {
    }
}
