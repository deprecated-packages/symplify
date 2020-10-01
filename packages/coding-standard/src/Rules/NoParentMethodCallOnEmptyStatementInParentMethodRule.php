<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Stmt\Nop;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use Symplify\CodingStandard\PhpParser\NodeNameResolver;
use Symplify\CodingStandard\PHPStan\NodeResolver\ParentClassMethodNodeResolver;

/**
 * @see \Symplify\CodingStandard\Tests\Rules\NoParentMethodCallOnEmptyStatementInParentMethodRule\NoParentMethodCallOnEmptyStatementInParentMethodRuleTest
 */
final class NoParentMethodCallOnEmptyStatementInParentMethodRule implements Rule
{
    /**
     * @var string
     */
    public const ERROR_MESSAGE = 'Do not call parent method if parent method is empty';

    /**
     * @var NodeNameResolver
     */
    private $nodeNameResolver;

    /**
     * @var ParentClassMethodNodeResolver
     */
    private $parentClassMethodNodeResolver;

    public function __construct(
        NodeNameResolver $nodeNameResolver,
        ParentClassMethodNodeResolver $parentClassMethodNodeResolver
    ) {
        $this->nodeNameResolver = $nodeNameResolver;
        $this->parentClassMethodNodeResolver = $parentClassMethodNodeResolver;
    }

    public function getNodeType(): string
    {
        return StaticCall::class;
    }

    /**
     * @param StaticCall $node
     * @return string[]
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $this->nodeNameResolver->isName($node->class, 'parent')) {
            return [];
        }

        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        $methodName = $this->nodeNameResolver->getName($node->name);
        if ($methodName === null) {
            return [];
        }

        $parentClassMethodNodes = $this->parentClassMethodNodeResolver->resolveParentClassMethodNodes(
            $scope,
            $methodName
        );

        $countStmts = 0;
        foreach ($parentClassMethodNodes as $stmt) {
            // ensure empty statement not counted
            if ($stmt instanceof Nop) {
                continue;
            }
            ++$countStmts;
        }

        if ($countStmts === 0) {
            return [self::ERROR_MESSAGE];
        }

        return [];
    }
}
