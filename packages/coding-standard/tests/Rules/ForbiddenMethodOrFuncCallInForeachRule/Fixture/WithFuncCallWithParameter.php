<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrFuncCallInForeachRule\Fixture;

$arg = 'value';
function getData($arg)
{
    return [];
}

foreach (getData($arg) as $key => $item) {

}