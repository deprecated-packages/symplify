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
use Symplify\Astral\TypeAwareNodeFinder;

final class CacheIfAnalyzer
{
    public function __construct(
        private TypeAwareNodeFinder $typeAwareNodeFinder,
    ) {
    }

    public function isDefaultNullAssign(If_ $if): bool
    {
        if ($if->else !== null) {
            return false;
        }

        /** @var BinaryOp[] $binaryOps */
        $binaryOps = $this->typeAwareNodeFinder->findInstanceOf($if->cond, BinaryOp::class);
        if ($this->hasIdenticalToNull($binaryOps)) {
            return true;
        }

        $empty = $this->typeAwareNodeFinder->findFirstInstanceOf($if->cond, Empty_::class);
        if ($empty instanceof Empty_) {
            return true;
        }

        $isset = $this->typeAwareNodeFinder->findFirstInstanceOf($if->cond, Isset_::class);
        return $isset instanceof Isset_;
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
