<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckClassNamespaceFollowPsr4Rule\Fixture;

class SkipAnonymousClass
{
    public function run()
    {
        $someClass = new class
        {
        };
    }
}
