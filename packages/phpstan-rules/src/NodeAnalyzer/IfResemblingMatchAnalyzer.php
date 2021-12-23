<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\BinaryOp\BooleanOr;
use PhpParser\Node\Expr\BinaryOp\Equal;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\PrettyPrinter\Standard;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PHPStanRules\ValueObject\Spotter\IfAndCondExpr;

final class IfResemblingMatchAnalyzer
{
    public function __construct(
        private Standard $printerStandard,
        private SimpleNodeFinder $simpleNodeFinder,
    ) {
    }

    /**
     * @param IfAndCondExpr[] $ifsAndCondExprs
     */
    public function isUniqueCompareBinaryConds(array $ifsAndCondExprs): bool
    {
        $comparedExprContent = [];

        foreach ($ifsAndCondExprs as $ifAndCondExpr) {
            if ($ifAndCondExpr->getCondExpr() === null) {
                continue;
            }

            $condExpr = $ifAndCondExpr->getCondExpr();
            if (! $condExpr instanceof BinaryOp) {
                return false;
            }

            if (! $this->hasExclusiveIdenticalorEqual($condExpr)) {
                return false;
            }

            // assuming the left is compared expression
            $comparedExprContent[] = $this->printerStandard->prettyPrintExpr($condExpr->left);
        }

        $uniqueComparedExprContent = array_unique($comparedExprContent);
        return count($uniqueComparedExprContent) === 1;
    }

    /**
     * @param IfAndCondExpr[] $ifsAndCondExprs
     */
    public function isReturnExprSameVariableAsAssigned(Expr $returnExpr, array $ifsAndCondExprs): bool
    {
        $printedReturnExpr = $this->printerStandard->prettyPrintExpr($returnExpr);

        foreach ($ifsAndCondExprs as $ifAndCondExpr) {
            $assign = $this->simpleNodeFinder->findFirstByType($ifAndCondExpr->getStmt(), Assign::class);
            if ($assign instanceof Assign) {
                $assignVar = $assign->var;
                while ($assignVar instanceof ArrayDimFetch) {
                    $assignVar = $assignVar->var;
                }

                $printedAssignVar = $this->printerStandard->prettyPrintExpr($assignVar);
                if ($printedAssignVar === $printedReturnExpr) {
                    continue;
                }

                return false;
            }

            return false;
        }

        return true;
    }

    private function hasExclusivelyCompare(BooleanOr $booleanOr): bool
    {
        if (! $this->isIdenticalOrEqual($booleanOr->left)) {
            return false;
        }

        return $this->isIdenticalOrEqual($booleanOr->right);
    }

    private function isIdenticalOrEqual(Expr $expr): bool
    {
        if ($expr instanceof BooleanOr) {
            return $this->hasExclusivelyCompare($expr);
        }

        if ($expr instanceof Identical) {
            return true;
        }

        return $expr instanceof Equal;
    }

    private function hasExclusiveIdenticalorEqual(BinaryOp $binaryOp): bool
    {
        // has only ==, === and || binaries?
        $nestedBinaryOps = $this->simpleNodeFinder->findByType($binaryOp, BinaryOp::class);

        foreach ($nestedBinaryOps as $nestedBinaryOp) {
            if ($nestedBinaryOp instanceof Identical) {
                continue;
            }

            if ($nestedBinaryOp instanceof Equal) {
                continue;
            }

            if ($nestedBinaryOp instanceof BooleanOr) {
                // only allowed
                if ($this->hasExclusivelyCompare($nestedBinaryOp)) {
                    continue;
                }

                return false;
            }

            return false;
        }

        return true;
    }
}
