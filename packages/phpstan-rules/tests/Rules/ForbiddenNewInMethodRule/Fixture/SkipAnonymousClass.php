<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\ForbiddenNewInMethodRule\Fixture;

use PhpParser\NodeVisitorAbstract;

final class SkipAnonymousClass
{
    public function run()
    {
        $someNodeVisitor = new class() extends NodeVisitorAbstract
        {
            public function anotherMethod()
            {
            }
        };
    }
}
