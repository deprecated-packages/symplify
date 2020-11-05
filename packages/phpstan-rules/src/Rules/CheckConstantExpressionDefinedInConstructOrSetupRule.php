<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\ConstExprEvaluationException;
use PhpParser\ConstExprEvaluator;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ValueObject\MethodName;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\CheckConstantExpressionDefinedInConstructOrSetupRuleTest
 */
final class CheckConstantExpressionDefinedInConstructOrSetupRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constant expression can be only defined in "__construct()" or "setUp()" method';

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var ConstExprEvaluator
     */
    private $constExprEvaluator;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(
        NodeFinder $nodeFinder,
        SimpleNameResolver $simpleNameResolver,
        ConstExprEvaluator $constExprEvaluator
    ) {
        $this->nodeFinder = $nodeFinder;
        $this->simpleNameResolver = $simpleNameResolver;
        $this->constExprEvaluator = $constExprEvaluator;
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
        $classMethod = $this->resolveCurrentClassMethod($node);
        if ($classMethod === null) {
            return [];
        }

        $parent = $node->getAttribute(PHPStanAttributeKey::PARENT);
        if ($this->isNotInsideClassMethodDirectly($parent)) {
            return [];
        }

        if ($this->isUsedInNextStatement($node, $parent)) {
            return [];
        }

        if ($this->simpleNameResolver->isNames($classMethod->name, [MethodName::CONSTRUCTOR, MethodName::SET_UP])) {
            return [];
        }

        if (! $this->isConstantExpr($node->expr)) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isConstantExpr(Expr $expr): bool
    {
        try {
            $this->constExprEvaluator->evaluateDirectly($expr);
            return true;
        } catch (ConstExprEvaluationException $constExprEvaluationException) {
            return false;
        }
    }

    private function isNotInsideClassMethodDirectly(Node $node): bool
    {
        $parentStatement = $node->getAttribute(PHPStanAttributeKey::PARENT);
        return ! $parentStatement instanceof ClassMethod;
    }

    private function isUsedInNextStatement(Assign $assign, Node $node): bool
    {
        $var = $assign->var;
        $varClass = get_class($var);
        $next = $node->getAttribute(PHPStanAttributeKey::NEXT);
        $parentOfParentAssignment = $node->getAttribute(PHPStanAttributeKey::PARENT);

        while ($next) {
            $nextVars = $this->nodeFinder->findInstanceOf($next, $varClass);
            if ($this->isHasSameVar($nextVars, $parentOfParentAssignment, $var)) {
                return true;
            }

            $next = $next->getAttribute(PHPStanAttributeKey::NEXT);
        }

        return false;
    }

    private function isHasSameVar(array $nodes, Node $parentOfParentAssignment, Node $var): bool
    {
        foreach ($nodes as $node) {
            $parentOfParentNode = $node->getAttribute(PHPStanAttributeKey::PARENT)
                ->getAttribute(PHPStanAttributeKey::PARENT);

            if (property_exists($node, 'name') && property_exists($var, 'name') && $node->name === $var->name
                && $parentOfParentNode !== $parentOfParentAssignment
                ) {
                return true;
            }
        }

        return false;
    }
}
