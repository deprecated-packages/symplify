<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\Fixture;

use PhpParser\Node\Expr\BinaryOp\BooleanAnd;

final class SkipSingleAnd
{
    public function run()
    {
        // &&
        return BooleanAnd::class;
    }
}
