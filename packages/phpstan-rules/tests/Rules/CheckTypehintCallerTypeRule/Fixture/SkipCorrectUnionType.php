<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Tests\Rules\CheckTypehintCallerTypeRule\Fixture;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Return_;

final class SkipCorrectUnionType
{
    public function run(\PhpParser\Node $node)
    {
        if (! $node instanceof Assign && ! $node instanceof Return_) {
            return [];
        }

        if (! $this->isIncludeOnceOrRequireOnce($node)) {
            return [];
        }

        return [];
    }

    /**
     * @param Assign|Return_ $node
     */
    private function isIncludeOnceOrRequireOnce(\PhpParser\Node $node)
    {
        return true;
    }
}
