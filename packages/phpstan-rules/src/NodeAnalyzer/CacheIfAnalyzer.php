<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\BinaryOp\NotIdentical;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\Empty_;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Stmt\If_;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;

final class CacheIfAnalyzer
{
    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder,
    ) {
    }

    public function isDefaultNullAssign(If_ $if): bool
    {
        if ($if->else !== null) {
            return false;
        }

        $binaryOps = $this->simpleNodeFinder->findByType($if->cond, BinaryOp::class);
        if ($this->hasIdenticalToNull($binaryOps)) {
            return true;
        }

        return $this->simpleNodeFinder->hasByTypes($if->cond, [Empty_::class, Isset_::class]);
    }

    /**
     * @param BinaryOp[] $binaryOps
     */
    private function hasIdenticalToNull(array $binaryOps): bool
    {
        foreach ($binaryOps as $binaryOp) {
            if (! $binaryOp instanceof Identical && ! $binaryOp instanceof NotIdentical) {
                continue;
            }

            if (! $binaryOp->right instanceof ConstFetch) {
                continue;
            }

            $constFetch = $binaryOp->right;
            if ($constFetch->name->toLowerString() === 'null') {
                return true;
            }
        }

        return false;
    }
}
