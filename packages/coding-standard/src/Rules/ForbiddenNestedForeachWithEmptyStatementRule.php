<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenNestedForeachWithEmptyStatementRule\ForbiddenNestedForeachWithEmptyStatementRuleTest
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

    public function isNextForeachWithEmptyStatement(Foreach_ $foreach): bool
    {
        $stmts = $this->nodeFinder->findInstanceOf($foreach, Stmt::class);
        if (! $stmts[1] instanceof Foreach_) {
            return false;
        }

        return true;
    }
}
