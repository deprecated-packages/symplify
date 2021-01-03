<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\TooDeepNewClassNestingRule\Fixture;

new A(
    new B(
        new C()
    )
);

new A(
    new B(),
    new C(),
);
