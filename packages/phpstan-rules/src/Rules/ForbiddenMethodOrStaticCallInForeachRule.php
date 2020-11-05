<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\PHPStanRules\Tests\Rules\ForbiddenMethodOrStaticCallInForeachRule\ForbiddenMethodOrStaticCallInForeachRuleTest
 */
final class ForbiddenMethodOrStaticCallInForeachRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method or Static call in foreach is not allowed.';

    /**
     * @var string[]
     */
    private const EXPRESSION_CLASS_TYPES = [MethodCall::class, StaticCall::class];

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
        foreach (self::EXPRESSION_CLASS_TYPES as $expressionClassType) {
            /** @var MethodCall[]|StaticCall[] $calls */
            $calls = $this->nodeFinder->findInstanceOf($node->expr, $expressionClassType);
            $isHasArgs = $this->isHasArgs($calls);

            if (! $isHasArgs) {
                continue;
            }

            return [self::ERROR_MESSAGE];
        }

        return [];
    }

    /**
     * @param MethodCall[]|StaticCall[] $calls
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
