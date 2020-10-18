<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrFuncCallInForeachRule\Fixture;

function getDataWithoutParameter()
{
    return [];
}

foreach (getDataWithoutParameter() as $key => $item) {

}