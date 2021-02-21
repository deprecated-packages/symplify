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
use PHPStan\Analyser\Scope;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\ParentNodeFinder;
use Symplify\Astral\NodeValue\NodeValueResolver;
use Symplify\PackageBuilder\ValueObject\MethodName;
use Symplify\PHPStanRules\NodeFinder\StatementFinder;
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

    /**
     * @var StatementFinder
     */
    private $statementFinder;

    public function __construct(
        SimpleNameResolver $simpleNameResolver,
        NodeValueResolver $nodeValueResolver,
        ParentNodeFinder $parentNodeFinder,
        StatementFinder $statementFinder
    ) {
        $this->simpleNameResolver = $simpleNameResolver;
        $this->parentNodeFinder = $parentNodeFinder;
        $this->nodeValueResolver = $nodeValueResolver;
        $this->statementFinder = $statementFinder;
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

        if ($this->statementFinder->isUsedInNextStatement($node, $parent)) {
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

        $value = $this->nodeValueResolver->resolve($expr, $scope->getFile());
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

    private function isInInstatiationClassMethod(Assign $assign): bool
    {
        $classMethod = $this->parentNodeFinder->findFirstParentByType($assign, ClassMethod::class);
        if (! $classMethod instanceof ClassMethod) {
            return true;
        }

        return $this->simpleNameResolver->isNames($classMethod->name, [MethodName::CONSTRUCTOR, MethodName::SET_UP]);
    }
}
