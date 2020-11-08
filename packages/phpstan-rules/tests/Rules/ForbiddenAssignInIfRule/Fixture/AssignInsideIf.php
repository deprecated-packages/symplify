<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAssignInIfRule\Fixture;

function data()
{
    return rand(1, 2);
}

if (($a = data()) === 1) {

}
