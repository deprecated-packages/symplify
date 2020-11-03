<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\TooDeepNewClassNestingRule\Fixture;

new A(
    new B(),
    new C(),
    new D(),
);

new A(
    new B(
        new C(),
        new D(),
        new E(),
    )
);
