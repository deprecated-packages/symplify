<?php

declare(strict_types=1);

namespace Symplify\Astral\Reflection;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\ThisType;
use Symplify\Astral\Naming\SimpleNameResolver;

/**
 * @api
 * @deprecated will be removed in next major release
 */
final class MethodCallParser
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ReflectionParser $reflectionParser
    ) {
    }

    public function parseMethodCall(MethodCall $methodCall, Scope $scope): ClassMethod|null
    {
        $callerType = $scope->getType($methodCall->var);

        if ($callerType instanceof ThisType) {
            $callerType = $callerType->getStaticObjectType();
        }

        if (! $callerType instanceof ObjectType) {
            return null;
        }

        $classReflection = $callerType->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        $methodName = $this->simpleNameResolver->getName($methodCall->name);
        if ($methodName === null) {
            return null;
        }

        if (! $classReflection->hasNativeMethod($methodName)) {
            return null;
        }

        $extendedMethodReflection = $classReflection->getNativeMethod($methodName);

        return $this->reflectionParser->parsePHPStanMethodReflection($extendedMethodReflection);
    }
}
