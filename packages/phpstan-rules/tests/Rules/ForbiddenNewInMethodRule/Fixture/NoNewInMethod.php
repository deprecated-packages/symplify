<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNewInMethodRule\Fixture;

final class NoNewInMethod
{
    public function run(): void
    {
        echo 'test';
    }
}
