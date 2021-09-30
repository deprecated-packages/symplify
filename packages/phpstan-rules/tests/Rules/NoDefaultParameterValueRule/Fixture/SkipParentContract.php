<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\NoDefaultParameterValueRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\NoDefaultParameterValueRule\Source\AbstractParentClassWithDefaultValue;

final class SkipParentContract extends AbstractParentClassWithDefaultValue
{
    public function run($value = true): void
    {
    }
}
