<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenMultipleClassLikeInOneFileRule\Fixture;

final class SkipOneClassWithAnonymousClass
{
    public function run()
    {
        $someClass = new class {};
    }
}
