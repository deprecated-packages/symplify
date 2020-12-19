<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Printer\NodeComparator;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\NoParentMethodCallOnNoOverrideProcessRule\NoParentMethodCallOnNoOverrideProcessRuleTest
 */
final class NoParentMethodCallOnNoOverrideProcessRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not call parent method if no override process';

    /**
     * @var NodeComparator
     */
    private $nodeComparator;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(SimpleNameResolver $simpleNameResolver, NodeComparator $nodeComparator)
    {
        $this->nodeComparator = $nodeComparator;
        $this->simpleNameResolver = $simpleNameResolver;
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

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
class SomeClass extends Printer
{
    public function print($nodes)
    {
        return parent::print($nodes);
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass extends Printer
{
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isParentSelfMethodStaticCall(Node $node, ClassMethod $classMethod): bool
    {
        if (! $node instanceof StaticCall) {
            return false;
        }

        if (! $this->simpleNameResolver->isName($node->class, 'parent')) {
            return false;
        }

        return $this->simpleNameResolver->areNamesEqual($node->name, $classMethod->name);
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
