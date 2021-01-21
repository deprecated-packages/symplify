<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Fixture;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use Symplify\PHPStanRules\Tests\Rules\PreferredClassRule\Fixture\SomeStaticCall;

class SkipRecursive
{
    /**
     * @param SomeStaticCall|MethodCall $node
     */
    public function process(Node $node)
    {
        $this->run($node);
    }

    /**
     * @param SomeStaticCall|MethodCall $node
     */
    private function run(Node $node)
    {
        if ($node->name instanceof MethodCall) {
            $this->run($node->name);
        }
    }
}
