<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Return_;
use Symplify\PHPStanRules\ValueObject\Spotter\IfAndCondExpr;
use Symplify\PHPStanRules\ValueObject\Spotter\ReturnAndAssignBranchCounts;

final class IfElseBranchAnalyzer
{
    /**
     * @param IfAndCondExpr[] $ifsAndCondExprs
     */
    public function resolveBranchTypesToCount(array $ifsAndCondExprs): ReturnAndAssignBranchCounts
    {
        $returnBranchCount = 0;
        $assignBranchCount = 0;

        foreach ($ifsAndCondExprs as $ifAndCondExpr) {
            // unwrap expression
            $stmt = $ifAndCondExpr->getStmt();
            if ($stmt instanceof Expression) {
                $stmt = $stmt->expr;
            }

            if ($stmt instanceof Return_) {
                ++$returnBranchCount;
            } elseif ($stmt instanceof Assign) {
                ++$assignBranchCount;
            }
        }

        return new ReturnAndAssignBranchCounts($returnBranchCount, $assignBranchCount);
    }
}
