<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StaticType;
use PHPStan\Type\TypeWithClassName;

final class MethodCallNodeAnalyzer
{
    public function resolveMethodCallReflection(MethodCall $methodCall, Scope $scope): ?PhpMethodReflection
    {
        $callerClassReflection = $this->resolveCallerClassReflection($scope, $methodCall);

        if (! $callerClassReflection instanceof ClassReflection) {
            return null;
        }

        if (! $methodCall->name instanceof Identifier) {
            return null;
        }

        $methodName = $methodCall->name->toString();

        $extendedMethodReflection = $callerClassReflection->getMethod($methodName, $scope);
        if (! $extendedMethodReflection instanceof PhpMethodReflection) {
            return null;
        }

        return $extendedMethodReflection;
    }

    private function resolveCallerClassReflection(Scope $scope, MethodCall $methodCall): ?ClassReflection
    {
        $callerType = $scope->getType($methodCall->var);
        if (! $callerType instanceof TypeWithClassName) {
            return null;
        }

        if ($callerType instanceof StaticType) {
            $callerType = $callerType->getStaticObjectType();
        }

        if (! $callerType instanceof ObjectType) {
            return null;
        }

        return $callerType->getClassReflection();
    }
}
