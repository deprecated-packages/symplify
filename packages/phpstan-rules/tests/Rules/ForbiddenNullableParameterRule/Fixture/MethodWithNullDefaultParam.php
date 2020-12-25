<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\Fixture;

final class MethodWithNullDefaultParam
{
    public function run(\PhpParser\Node $defaultValue = null): void
    {
    }
}
