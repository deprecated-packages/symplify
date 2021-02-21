<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableReturnRule\Fixture;

final class MethodWithNullableReturn
{
    public function run(): ?\PhpParser\Node
    {
    }
}
