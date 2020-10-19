<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrFuncCallInIfRule\Fixture;

function getDataWithoutParameter()
{
    return [];
}

if (getDataWithoutParameter() === []) {

} elseif (getDataWithoutParameter() !== []) {

}
