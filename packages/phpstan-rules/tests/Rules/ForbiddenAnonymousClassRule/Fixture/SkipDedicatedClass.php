<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAnonymousClassRule\Fixture;

final class SkipDedicatedClass
{
    public function run(): void
    {
        new SkipDedicatedClass();
    }
}
