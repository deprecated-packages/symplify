<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbidMethodOrFuncCallInForeachRule\Fixture;

function getData()
{
    return [];
}

foreach (getData() as $key => $item) {

}