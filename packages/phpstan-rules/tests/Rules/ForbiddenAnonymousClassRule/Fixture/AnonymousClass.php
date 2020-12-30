<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenAnonymousClassRule\Fixture;

final class AnonymousClass
{
    public function run()
    {
        new class {};
    }
}
