<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Else_;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\If_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoMultiArrayAssignRule\NoMultiArrayAssignRuleTest
 * @implements Rule<Node>
 */
final class NoMultiArrayAssignRule implements Rule, DocumentedRuleInterface
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Use value object over multi array assign';

    public function __construct(
        private NodeComparator $nodeComparator
    ) {
    }

    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @return RuleError[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        // check all of stmts aware nodes, see https://github.com/nikic/PHP-Parser/pull/836
        if (! $node instanceof ClassMethod && ! $node instanceof Function_ && ! $node instanceof Closure && ! $node instanceof If_ && ! $node instanceof Else_) {
            return [];
        }

        foreach ((array) $node->stmts as $key => $stmt) {
            $firstArrayDimFetch = $this->matchAssignToArrayDimFetch($stmt);
            if (! $firstArrayDimFetch instanceof ArrayDimFetch) {
                continue;
            }

            $nextStmt = $node->stmts[$key + 1] ?? null;
            if (! $nextStmt instanceof Stmt) {
                return [];
            }

            $secondArrayDimFetch = $this->matchAssignToArrayDimFetch($nextStmt);
            if (! $secondArrayDimFetch instanceof ArrayDimFetch) {
                continue;
            }

            if (! $this->haveSameArrayDimFetchNonEmptyRoot($firstArrayDimFetch, $secondArrayDimFetch)) {
                continue;
            }

            $ruleError = RuleErrorBuilder::message(self::ERROR_MESSAGE)
                ->line($nextStmt->getLine())
                ->build();

            return [$ruleError];
        }

        return [];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$values = [];
$values['person']['name'] = 'Tom';
$values['person']['surname'] = 'Dev';
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$values = [];
$values[] = new Person('Tom', 'Dev');
CODE_SAMPLE
            ),
        ]);
    }

    private function haveSameArrayDimFetchNonEmptyRoot(
        ArrayDimFetch $firstArrayDimFetch,
        ArrayDimFetch $secondArrayDimFetch
    ): bool {
        $singleNestedFirstArrayDimFetch = $this->resolveSingleNestedArrayDimFetch($firstArrayDimFetch);

        if ($singleNestedFirstArrayDimFetch->dim === null) {
            return false;
        }

        $singleNestedSecondArrayDimFetch = $this->resolveSingleNestedArrayDimFetch($secondArrayDimFetch);

        return $this->nodeComparator->areNodesEqual($singleNestedFirstArrayDimFetch, $singleNestedSecondArrayDimFetch);
    }

    private function resolveSingleNestedArrayDimFetch(ArrayDimFetch $arrayDimFetch): ArrayDimFetch
    {
        while ($arrayDimFetch->var instanceof ArrayDimFetch) {
            $arrayDimFetch = $arrayDimFetch->var;
        }

        return $arrayDimFetch;
    }

    private function matchAssignToArrayDimFetch(Stmt $stmt): ?ArrayDimFetch
    {
        if (! $stmt instanceof Expression) {
            return null;
        }

        if (! $stmt->expr instanceof Assign) {
            return null;
        }

        $assign = $stmt->expr;
        if (! $assign->var instanceof ArrayDimFetch) {
            return null;
        }

        return $assign->var;
    }
}
