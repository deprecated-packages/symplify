<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Explicit\SameNamedParamFamilyRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\Explicit\SameNamedParamFamilyRule\Source\WithNullParam;

final class SkipParentExtraNullableParam extends WithNullParam
{
    private function run($name)
    {
    }
}
