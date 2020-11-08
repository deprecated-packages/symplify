<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassMethodsNode;
use PHPStan\Type\TypeWithClassName;

final class ClassMethodsNodeAnalyzer
{
    /**
     * @return MethodCall[]
     */
    public function resolveMethodCallsByType(ClassMethodsNode $classMethodsNode, string $desiredType): array
    {
        $phpStanMethodCalls = $classMethodsNode->getMethodCalls();

        $methodCallsByType = [];
        foreach ($phpStanMethodCalls as $phpStanMethodCall) {
            $methodCall = $phpStanMethodCall->getNode();
            if (! $methodCall instanceof MethodCall) {
                continue;
            }

            if (! $this->isMethodCallWithCallerOfType($methodCall, $phpStanMethodCall->getScope(), $desiredType)) {
                continue;
            }

            $methodCallsByType[] = $methodCall;
        }

        return $methodCallsByType;
    }

    private function isMethodCallWithCallerOfType(MethodCall $methodCall, Scope $scope, string $desiredType): bool
    {
        if (! $methodCall->var instanceof Expr) {
            return false;
        }

        $callerType = $scope->getType($methodCall->var);
        if (! $callerType instanceof TypeWithClassName) {
            return false;
        }

        return is_a($callerType->getClassName(), $desiredType, true);
    }
}
