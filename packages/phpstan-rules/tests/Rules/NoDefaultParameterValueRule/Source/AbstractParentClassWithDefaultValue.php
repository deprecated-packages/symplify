<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDefaultParameterValueRule\Source;

abstract class AbstractParentClassWithDefaultValue
{
    public function run($value = true): void
    {
    }
}
