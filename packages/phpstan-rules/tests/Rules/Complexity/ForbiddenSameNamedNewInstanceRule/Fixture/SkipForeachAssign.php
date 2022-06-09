<?php
declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\Complexity\ForbiddenSameNamedNewInstanceRule\Fixture;

use PhpParser\Node\Expr\ArrayDimFetch;

final class SkipForeachAssign
{
    public function run(ArrayDimFetch $arrayDimFetch)
    {
        while ($arrayDimFetch->var instanceof ArrayDimFetch) {
            $arrayDimFetch = $arrayDimFetch->var;
        }
    }
}
