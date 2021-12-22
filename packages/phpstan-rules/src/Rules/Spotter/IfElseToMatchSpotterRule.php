<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Spotter;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\If_;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use Symplify\Astral\ValueObject\AttributeKey;
use Symplify\PHPStanRules\NodeAnalyzer\IfElseBranchAnalyzer;
use Symplify\PHPStanRules\NodeAnalyzer\IfResemblingMatchAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\ValueObject\Spotter\IfAndCondExpr;
use Symplify\PHPStanRules\ValueObject\Spotter\ReturnAndAssignBranchCounts;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see https://www.php.net/manual/en/control-structures.match.php
 *
 * @see \Symplify\PHPStanRules\Tests\Rules\Spotter\IfElseToMatchSpotterRule\IfElseToMatchSpotterRuleTest
 */
final class IfElseToMatchSpotterRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'If/else construction can be replace with more robust match()';

    public function __construct(
        private IfElseBranchAnalyzer $ifElseBranchAnalyzer,
        private IfResemblingMatchAnalyzer $ifResemblingMatchAnalyzer
    ) {
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [If_::class];
    }

    /**
     * @param If_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if ($this->shouldSkipIf($node)) {
            return [];
        }

        $branches = $this->mergeIfBranches($node);

        $ifsAndConds = [];
        foreach ($branches as $branch) {
            // must be exactly single item
            if (count($branch->stmts) !== 1) {
                return [];
            }

            // the conditioned parameters must be the same
            if ($branch instanceof If_ || $branch instanceof ElseIf_) {
                $ifsAndConds[] = new IfAndCondExpr($branch->stmts[0], $branch->cond);
                continue;
            }

            $ifsAndConds[] = new IfAndCondExpr($branch->stmts[0], null);
        }

        if (! $this->ifResemblingMatchAnalyzer->isUniqueBinaryConds($ifsAndConds)) {
            return [];
        }

        if ($this->isDefaultNullPropertyAssign($node)) {
            return [];
        }

        if ($this->shouldSkipForConflictingReturn($node, $ifsAndConds)) {
            return [];
        }

        $returnAndAssignBranchCounts = $this->ifElseBranchAnalyzer->resolveBranchTypesToCount($ifsAndConds);

        $branchCount = count($branches);
        if (! $this->isUnitedMatchingBranchType($returnAndAssignBranchCounts, $branchCount)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function spot($value)
    {
        if ($value === 100) {
            $items = ['yes'];
        } else {
            $items = ['no'];
        }

        return $items;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    public function spot($value)
    {
        return match($value) {
            100 => ['yes'],
            default => ['no'],
        };
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isUnitedMatchingBranchType(
        ReturnAndAssignBranchCounts $returnAndAssignBranchCounts,
        int $branchCount
    ): bool {
        if ($returnAndAssignBranchCounts->getAssignTypeCount() === $branchCount) {
            return true;
        }

        return $returnAndAssignBranchCounts->getReturnTypeCount() === $branchCount;
    }

    private function shouldSkipIf(If_ $if): bool
    {
        if ($if->else === null) {
            // is followed by return?
            $next = $if->getAttribute(AttributeKey::NEXT);
            return ! $next instanceof Return_;
        }

        return $if->elseifs === [];
    }

    /**
     * @param IfAndCondExpr[] $ifsAndCondExprs
     */
    private function shouldSkipForConflictingReturn(If_ $if, array $ifsAndCondExprs): bool
    {
        if ($if->else !== null) {
            return false;
        }

        $next = $if->getAttribute(AttributeKey::NEXT);
        if (! $next instanceof Return_) {
            return false;
        }

        $returnExpr = $next->expr;
        if (! $returnExpr instanceof Expr) {
            return false;
        }

        return ! $this->ifResemblingMatchAnalyzer->isReturnExprSameVariableAsAssigned($returnExpr, $ifsAndCondExprs);
    }

    /**
     * @return array<If_|Else_|ElseIf_>
     */
    private function mergeIfBranches(If_ $if): array
    {
        // all branches must have return or assign - at the same time

        /** @var array<If_|Else_|ElseIf_> $branches */
        $branches = array_merge([$if], $if->elseifs);
        if ($if->else instanceof Else_) {
            $branches[] = $if->else;
        }

        return $branches;
    }

    private function isDefaultNullPropertyAssign(If_ $if): bool
    {
        if ($if->else !== null) {
            return false;
        }

        if (! $if->cond instanceof BinaryOp) {
            return false;
        }

        $binaryOp = $if->cond;
        if (! $binaryOp->left instanceof PropertyFetch) {
            return false;
        }

        if (! $binaryOp->right instanceof ConstFetch) {
            return false;
        }

        $constFetch = $binaryOp->right;
        return $constFetch->name->toString() === 'null';
    }
}
