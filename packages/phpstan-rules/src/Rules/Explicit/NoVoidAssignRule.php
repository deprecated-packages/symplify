<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules\Explicit;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Yield_;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Return_;
use PHPStan\Analyser\Scope;
use PHPStan\Type\VoidType;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\Astral\Reflection\MethodCallParser;
use Symplify\PHPStanRules\Rules\AbstractSymplifyRule;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\Explicit\NoVoidAssignRule\NoVoidAssignRuleTest
 */
final class NoVoidAssignRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Assign of void value is not allowed, as it can lead to unexpected results';

    public function __construct(
        private MethodCallParser $methodCallParser,
        private SimpleNodeFinder $simpleNodeFinder,
    ) {
    }

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        $value = $this->getNothing();
    }

    public function getNothing(): void
    {
    }
}
CODE_SAMPLE
            ,
                <<<'CODE_SAMPLE'
final class SomeClass
{
    public function run()
    {
        $this->getNothing();
    }

    public function getNothing(): void
    {
    }
}
CODE_SAMPLE
            ),

        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Assign::class];
    }

    /**
     * @param Assign $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $assignedExprType = $scope->getType($node->expr);
        if ($assignedExprType instanceof VoidType) {
            return [self::ERROR_MESSAGE];
        }

        if (! $node->expr instanceof MethodCall) {
            return [];
        }

        return $this->processMethodCall($node->expr, $scope);
    }

    /**
     * @return string[]
     */
    private function processMethodCall(MethodCall $methodCall, Scope $scope): array
    {
        $classMethod = $this->methodCallParser->parseMethodCall($methodCall, $scope);

        // unable to analyse
        if ($classMethod === null) {
            return [];
        }

        if ($this->hasNonEmptyNonVoidReturnType($classMethod)) {
            return [];
        }

        // is not void
        if ($this->simpleNodeFinder->hasByTypes($classMethod, [Return_::class, Yield_::class])) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function hasNonEmptyNonVoidReturnType(ClassMethod $classMethod): bool
    {
        $returnTypeNode = $classMethod->returnType;
        if (! $returnTypeNode instanceof Node) {
            return false;
        }

        // not void
        if (! $returnTypeNode instanceof Identifier) {
            return true;
        }

        // not void
        return $returnTypeNode->toString() !== 'void';
    }
}
