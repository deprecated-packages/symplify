<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Scalar;
use PhpParser\Node\Scalar\MagicConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use Symplify\PHPStanRules\ValueObject\PHPStanAttributeKey;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\CheckConstantExpressionDefinedInConstructOrSetupRule\CheckConstantExpressionDefinedInConstructOrSetupRuleTest
 */
final class CheckConstantExpressionDefinedInConstructOrSetupRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Constant expression should only defined in __construct() or setUp()';

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
        if ($this->isNotInsideClassMethodDirectly($parent) || $this->isUsedInNextStatement($node, $parent)) {
            return [];
        }

        if (in_array(strtolower((string) $classMethod->name), ['__construct', 'setup'], true)) {
            return [];
        }

        if ($this->isMayNotAllowedConcat($node)) {
            return [self::ERROR_MESSAGE];
        }

        if (! $node->expr instanceof ClassConstFetch) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    private function isMayNotAllowedConcat(Assign $assign): bool
    {
        if ($assign->expr instanceof Concat) {
            if ($assign->expr->left instanceof Scalar && $assign->expr->right instanceof Scalar) {
                return true;
            }

            if ($assign->expr->left instanceof MagicConst && $assign->expr->right instanceof MethodCall) {
                return false;
            }
            return ! (! $assign->expr->left instanceof ClassConstFetch && ! $assign->expr->right instanceof ClassConstFetch);
        }

        return false;
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
            if (
                property_exists($node, 'name')
                && property_exists($var, 'name')
                && $node->name === $var->name
                && $parentOfParentNode !== $parentOfParentAssignment
                ) {
                return true;
            }
        }

        return false;
    }
}
