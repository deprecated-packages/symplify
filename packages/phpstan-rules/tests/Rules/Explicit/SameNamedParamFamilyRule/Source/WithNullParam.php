<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\SameNamedParamFamilyRule\Source;

abstract class WithNullParam
{
    private function run($name, $surname = null)
    {
    }
}
