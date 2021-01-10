<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\For_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PHPStanRules\NodeFinder\ParentNodeFinder;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\CheckConstantExpressionDefinedInConstructOrSetupRuleTest
 */
final class CheckConstantExpressionDefinedInConstructOrSetupRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Move constant expression to __construct(), setUp() method or constant';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    /**
     * @var ParentNodeFinder
     */
    private $parentNodeFinder;

    /**
     * @var NodeValueResolver
     */
    private $nodeValueResolver;

    public function __construct(
        NodeFinder $nodeFinder,
        SimpleNameResolver $simpleNameResolver,
        NodeValueResolver $nodeValueResolver,
        ParentNodeFinder $parentNodeFinder
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->parentNodeFinder = $parentNodeFinder;
        $this->nodeValueResolver = $nodeValueResolver;
    }

    /**
     * @return string[]
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
        if (! $node->var instanceof Variable) {
            return [];
        }

        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        if (! $parent instanceof Node) {
            return [];
        }

        if ($parent instanceof For_) {
            return [];
        }

        if ($this->isNotInsideClassMethodDirectly($parent)) {
            return [];
        }

        if ($this->isUsedInNextStatement($node, $parent)) {
            return [];
        }

        if ($this->isInInstatiationClassMethod($node)) {
            return [];
        }

        if (! $this->isConstantExpr($node->expr, $scope)) {
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
    public function someMethod()
    {
        $mainPath = getcwd() . '/absolute_path';
        return __DIR__ . $mainPath;
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
class SomeClass
{
    private $mainPath;

    public function __construct()
    {
        $this->mainPath = getcwd() . '/absolute_path';
    }

    public function someMethod()
    {
        return $this->mainPath;
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    private function isConstantExpr(Expr $expr, Scope $scope): bool
    {
        if ($expr instanceof ClassConstFetch) {
            return false;
        }

        $value = $this->nodeValueResolver->resolve($expr, $scope);
        if ($value === null) {
            return false;
        }

        return $value !== '';
    }

    private function isNotInsideClassMethodDirectly(Node $node): bool
    {
        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        return ! $parent instanceof ClassMethod;
    }

    private function isUsedInNextStatement(Assign $assign, Node $node): bool
    {
        $var = $assign->var;
        $varClass = get_class($var);
        $next = $node->getAttribute(PHPStanAttributeKey::NEXT);
        $parentOfParentAssignment = $node->getAttribute(PHPStanAttributeKey::PARENT);

        while ($next) {
            $nextVars = $this->nodeFinder->findInstanceOf($next, $varClass);
            if ($this->hasSameVar($nextVars, $parentOfParentAssignment, $var)) {
                return true;
            }

            $next = $next->getAttribute(PHPStanAttributeKey::NEXT);
        }

        return false;
    }

    /**
     * @param Node[] $nodes
     */
    private function hasSameVar(array $nodes, Node $parentOfParentAssignNode, Expr $varExpr): bool
    {
        foreach ($nodes as $node) {
            $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
            $parentOfParentNode = $parent->getAttribute(PHPStanAttributeKey::PARENT);

            if (! $this->simpleNameResolver->areNamesEqual($node, $varExpr)) {
                continue;
            }

            if ($parentOfParentNode !== $parentOfParentAssignNode) {
                return true;
            }
        }

        return false;
    }

    private function isInInstatiationClassMethod(Assign $assign): bool
    {
        $classMethod = $this->parentNodeFinder->getFirstParentByType($assign, ClassMethod::class);
        if (! $classMethod instanceof ClassMethod) {
            return true;
        }

        return $this->simpleNameResolver->isNames($classMethod->name, [MethodName::CONSTRUCTOR, MethodName::SET_UP]);
    }
}
