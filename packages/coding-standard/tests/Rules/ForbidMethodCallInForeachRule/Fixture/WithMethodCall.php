<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbidMethodCallInForeachRule\Fixture;

function getData()
{
    return [];
}

foreach ($this->getData() as $key => $item) {

}