<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;
use Symplify\CodingStandard\PHPStan\NodeComparator;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\NoParentMethodCallOnNoOverrideProcessRuleTest
 */
final class NoParentMethodCallOnNoOverrideProcessRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not call parent method if no override process';

    /**
     * @var NodeNameResolver
     */
    private $nodeNameResolver;

    /**
     * @var NodeComparator
     */
    private $nodeComparator;

    public function __construct(NodeNameResolver $nodeNameResolver, NodeComparator $nodeComparator)
    {
        $this->nodeNameResolver = $nodeNameResolver;
        $this->nodeComparator = $nodeComparator;
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [ClassMethod::class];
    }

    /**
     * @param ClassMethod $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $onlyNode = $this->resolveOnlyNode($node);
        if (! $onlyNode instanceof StaticCall) {
            return [];
        }

        if (! $this->isParentSelfMethodStaticCall($onlyNode, $node)) {
            return [];
        }

        $methodCallArgs = (array) $onlyNode->args;
        $classMethodParams = (array) $node->params;

        if (! $this->nodeComparator->areArgsAndParamsSame($methodCallArgs, $classMethodParams)) {
            return [];
        }
        return [self::ERROR_MESSAGE];
    }

    private function isParentSelfMethodStaticCall(Node $node, ClassMethod $classMethod): bool
    {
        if (! $node instanceof StaticCall) {
            return false;
        }

        if (! $this->nodeNameResolver->isName($node->class, 'parent')) {
            return false;
        }

        return $this->nodeNameResolver->areNamesEquals($node->name, $classMethod->name);
    }

    private function resolveOnlyNode(ClassMethod $classMethod): ?Node
    {
        $stmts = (array) $classMethod->stmts;
        if (count($stmts) !== 1) {
            return null;
        }

        $onlyStmt = $stmts[0];
        if (! $onlyStmt instanceof Expression) {
            return null;
        }

        return $onlyStmt->expr;
    }
}
