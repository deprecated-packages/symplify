<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenNewInMethodRule\Fixture;

final class NoNewInMethod
{
    public function run(): void
    {
        echo 'test';
    }
}
