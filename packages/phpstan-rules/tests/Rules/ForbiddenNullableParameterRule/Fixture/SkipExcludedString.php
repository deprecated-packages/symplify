<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\Fixture;

use PhpParser\Node\Scalar\String_;

final class SkipExcludedString
{
    public function run(?String_ $defaultValue = null): void
    {
    }
}
