<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableParameterRule\Fixture;

final class MethodWithNullableParam
{
    public function run(?\PhpParser\Node $value = null): void
    {
    }
}
