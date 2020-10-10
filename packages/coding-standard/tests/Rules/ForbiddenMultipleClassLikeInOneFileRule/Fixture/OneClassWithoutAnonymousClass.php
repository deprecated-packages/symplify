<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Rules\ForbiddenMultipleClassLikeInOneFileRule\Fixture;

final class OneClassWithoutAnonymousClass
{
    public function run()
    {
        $someClass = new class {};
    }
}
