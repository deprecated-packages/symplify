<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenAssignInifRule\Fixture;

function data()
{
    return rand(1, 2);
}

$a = data();
if ($a === 1) {
    $a = 2;
}