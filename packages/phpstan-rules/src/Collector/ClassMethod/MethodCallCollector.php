<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Type\ThisType;
use PHPStan\Type\TypeWithClassName;

/**
 * @implements Collector<MethodCall, array<string[]>>
 */
final class MethodCallCollector implements Collector
{
    public function getNodeType(): string
    {
        return MethodCall::class;
    }

    /**
     * @param MethodCall $node
     * @return array<array{class-string, string, int}>|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if ($node->name instanceof Expr) {
            return null;
        }

        $callerType = $scope->getType($node->var);

        // skip self calls, as external is needed to make the method public
        if ($callerType instanceof ThisType) {
            return null;
        }

        if (! $callerType instanceof TypeWithClassName) {
            return null;
        }

        $methodName = $node->name->toString();
        return [$callerType->getClassName() . '::' . $methodName];
    }
}
