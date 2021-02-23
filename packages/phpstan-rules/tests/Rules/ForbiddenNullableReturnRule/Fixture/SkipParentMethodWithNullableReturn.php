<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableReturnRule\Fixture;

use Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableReturnRule\Source\ParentClassWithNullableReturn;

final class SkipParentMethodWithNullableReturn extends ParentClassWithNullableReturn
{
    public function returnType(): ?\PhpParser\Node
    {
    }
}
