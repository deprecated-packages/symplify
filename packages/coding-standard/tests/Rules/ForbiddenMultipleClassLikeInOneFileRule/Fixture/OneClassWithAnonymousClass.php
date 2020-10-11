<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMultipleClassLikeInOneFileRule\Fixture;

final class OneClassWithAnonymousClass
{
    public function run()
    {
        $someClass = new class {};
    }
}
