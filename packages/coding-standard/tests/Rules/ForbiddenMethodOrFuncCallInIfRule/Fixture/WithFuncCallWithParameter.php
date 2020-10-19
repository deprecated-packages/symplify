<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrFuncCallInIfRule\Fixture;

$arg = 'value';
function getData($arg)
{
    return [];
}

if (getData($arg) === []) {

} elseif (getData($arg) !== []) {

}
