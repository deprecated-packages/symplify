<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNewOutsideFactoryServiceRule\Fixture;

final class SkipNonStarFactory
{
    public function create()
    {
        return new Foo();
    }
}
