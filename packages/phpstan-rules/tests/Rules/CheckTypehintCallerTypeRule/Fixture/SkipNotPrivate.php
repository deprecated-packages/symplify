<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;

class SkipNotPrivate
{
    public function run(Node $node)
    {
        if ($node instanceof MethodCall) {
            $this->isCheckNotPrivate($node);
        }
    }

    public function isCheckNotPrivate(Node $node)
    {
    }
}
