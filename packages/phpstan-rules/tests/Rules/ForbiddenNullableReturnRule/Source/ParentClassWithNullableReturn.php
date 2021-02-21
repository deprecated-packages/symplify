<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNullableReturnRule\Source;

abstract class ParentClassWithNullableReturn
{
    public function returnType(): ?\PhpParser\Node
    {
    }
}
