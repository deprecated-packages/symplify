<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Foreach_;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\ForbidMethodOrFuncCallInForeachRule\ForbidMethodOrFuncCallInForeachRuleTest
 */
final class ForbidMethodOrFuncCallInForeachRule extends AbstractSymplifyRule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Method or Function call in foreach is not allowed.';

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
     * @return string[]
     */
    public function process(Node $node, Scope $scope): array
    {
        if (count($this->nodeFinder->findInstanceOf($node->expr, MethodCall::class)) > 0) {
            return [self::ERROR_MESSAGE];
        }

        if (count($this->nodeFinder->findInstanceOf($node->expr, FuncCall::class)) > 0) {
            return [self::ERROR_MESSAGE];
        }

        return [];
    }
}
