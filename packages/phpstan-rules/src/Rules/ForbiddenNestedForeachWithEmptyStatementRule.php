<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenNestedForeachWithEmptyStatementRule\ForbiddenNestedForeachWithEmptyStatementRuleTest
 */
final class ForbiddenNestedForeachWithEmptyStatementRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Nested foreach with empty statement is not allowed';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(NodeFinder $nodeFinder)
    {
        $this->nodeFinder = $nodeFinder;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [Foreach_::class];
    }

    /**
     * @param Foreach_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (! $this->isNextForeachWithEmptyStatement($node)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$collectedFileErrors = [];

foreach ($errors as $fileErrors) {
    foreach ($fileErrors as $fileError) {
        $collectedFileErrors[] = $fileError;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$collectedFileErrors = [];

foreach ($fileErrors as $fileError) {
    $collectedFileErrors[] = $fileError;
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isNextForeachWithEmptyStatement(Foreach_ $foreach): bool
    {
        $stmts = $this->nodeFinder->findInstanceOf($foreach->stmts, Stmt::class);
        if (! isset($stmts[0])) {
            return false;
        }

        if (! $stmts[0] instanceof Foreach_) {
            return false;
        }

        /** @var Variable $foreachVariable */
        $foreachVariable = $foreach->expr->getAttribute(PHPStanAttributeKey::NEXT);

        /** @var Variable $nextForeachVariable */
        $nextForeachVariable = $stmts[0]->expr;

        return $foreachVariable->name === $nextForeachVariable->name;
    }
}
