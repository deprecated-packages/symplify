<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNewInMethodRule\Fixture;

final class HasNewInMethod
{
    public function run(): void
    {
        new \stdClass();
    }
}
