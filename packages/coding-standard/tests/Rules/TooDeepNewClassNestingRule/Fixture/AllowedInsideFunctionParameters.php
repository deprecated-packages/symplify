<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\TooDeepNewClassNestingRule\Fixture;

function foo(A $a, B $b, C $c, D $d, E $e)
{

}

foo(new A, new B, new C, new D, new E);
