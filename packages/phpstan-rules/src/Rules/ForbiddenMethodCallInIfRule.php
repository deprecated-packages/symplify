<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Type\BooleanType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodCallInIfRule\ForbiddenMethodCallInIfRuleTest
 */
final class ForbiddenMethodCallInIfRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method call in if or elseif is not allowed.';

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
        return [If_::class, ElseIf_::class];
    }

    /**
     * @param If_|ElseIf_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        /** @var MethodCall[] $calls */
        $calls = $this->nodeFinder->findInstanceOf($node->cond, MethodCall::class);
        $isHasArgs = $this->isHasArgs($calls, $scope);

        if (! $isHasArgs) {
            return [];
        }

        return [self::ERROR_MESSAGE];
    }

    /**
     * @param MethodCall[] $calls
     */
    private function isHasArgs(array $calls, Scope $scope): bool
    {
        foreach ($calls as $call) {
            if ($call->args === []) {
                continue;
            }

            /** @var ObjectType $type */
            $type = $scope->getType($call->var);

            if ($call->var instanceof PropertyFetch) {
                /** @var ObjectType|ThisType $type */
                $type = $scope->getType($call->var);
            }

            if ($type instanceof ThisType) {
                continue;
            }

            $callType = $scope->getType($call);
            if ($callType instanceof BooleanType) {
                continue;
            }

            return true;
        }

        return false;
    }
}
