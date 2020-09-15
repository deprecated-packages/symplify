<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenFuncCallRule\Fixture;

final class ExtractCall
{
    public function run($items)
    {
        extract($items);
    }
}
