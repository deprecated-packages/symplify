<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Spotter;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\If_;
use PhpParser\PrettyPrinter\Standard;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\NodeAnalyzer\IfElseBranchAnalyzer;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\PHPStanRules\ValueObject\Spotter\IfAndCond;
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
        private Standard $printerStandard,
        private IfElseBranchAnalyzer $ifElseBranchAnalyzer
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

        // all branches must have return or assign - at the same time
        /** @var array<If_|Else_|ElseIf_> $branches */
        $branches = array_merge([$node], $node->elseifs, [$node->else]);

        $ifsAndConds = [];

        foreach ($branches as $branch) {
            // must be exactly single item
            if (count($branch->stmts) !== 1) {
                return [];
            }

            // the conditioned parameters must be the same
            if ($branch instanceof If_ || $branch instanceof ElseIf_) {
                $ifsAndConds[] = new IfAndCond($branch->stmts[0], $branch->cond);
                continue;
            }

            $ifsAndConds[] = new IfAndCond($branch->stmts[0], null);
        }

        if (! $this->isUniqueBinaryConds($ifsAndConds)) {
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
            return true;
        }

        return $if->elseifs === [];
    }

    /**
     * @param IfAndCond[] $ifsAndConds
     */
    private function isUniqueBinaryConds(array $ifsAndConds): bool
    {
        $comparedExprContent = [];

        foreach ($ifsAndConds as $ifsAndCond) {
            if ($ifsAndCond->getCondExpr() === null) {
                continue;
            }

            $condExpr = $ifsAndCond->getCondExpr();
            if (! $condExpr instanceof BinaryOp) {
                return false;
            }

            // assuming the left is compared expression
            $comparedExprContent[] = $this->printerStandard->prettyPrintExpr($condExpr->left);
        }

        $uniqueComparedExprContent = array_unique($comparedExprContent);
        return count($uniqueComparedExprContent) === 1;
    }
}
