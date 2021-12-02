<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp;
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
    public function isUniqueBinaryConds(array $ifsAndCondExprs): bool
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
}
