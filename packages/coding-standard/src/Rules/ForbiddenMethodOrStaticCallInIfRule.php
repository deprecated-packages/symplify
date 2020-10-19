<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\ElseIf_;
use PhpParser\Node\Stmt\If_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbiddenMethodOrStaticCallInIfRule\ForbiddenMethodOrStaticCallInIfRuleTest
 */
final class ForbiddenMethodOrStaticCallInIfRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method or Static call in if or elseif is not allowed.';

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
     * @param If_ $node
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        $expressionClasses = [MethodCall::class, StaticCall::class];

        foreach ($expressionClasses as $expressionClass) {
            /** @var MethodCall[]|StaticCall[]|FuncCall[] $calls */
            $calls = $this->nodeFinder->findInstanceOf($node->cond, $expressionClass);
            $isHasArgs = $this->isHasArgs($calls);

            if (! $isHasArgs) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    /**
     * @param MethodCall[]|StaticCall[]|FuncCall[] $calls
     */
    private function isHasArgs(array $calls): bool
    {
        foreach ($calls as $call) {
            if ($call->args !== []) {
                return true;
            }
        }

        return false;
    }
}
