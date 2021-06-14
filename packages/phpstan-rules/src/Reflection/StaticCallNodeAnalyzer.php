<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\MixedType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Symplify\Astral\Naming\SimpleNameResolver;

final class StaticCallNodeAnalyzer
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function isAbstractMethodStaticCall(Expr $expr, Scope $scope): bool
    {
        if (! $expr instanceof StaticCall) {
            return false;
        }

        $callerType = $this->resolveStaticCallCallerType($expr, $scope);
        $methodName = $this->simpleNameResolver->getName($expr->name);

        if ($methodName === null) {
            return false;
        }

        foreach ($callerType->getReferencedClasses() as $referencedClass) {
            if (! $this->reflectionProvider->hasClass($referencedClass)) {
                continue;
            }

            $classReflection = $this->reflectionProvider->getClass($referencedClass);

            if (! $classReflection->hasMethod($methodName)) {
                continue;
            }

            $methodReflection = $classReflection->getMethod($methodName, $scope);
            if (! $methodReflection instanceof PhpMethodReflection) {
                continue;
            }

            // cannot replace abstract call with direct one
            if ($methodReflection->isAbstract()) {
                return true;
            }
        }

        return false;
    }

    private function resolveStaticCallCallerType(StaticCall $staticCall, Scope $scope): Type
    {
        if ($staticCall->class instanceof Name) {
            $className = $this->simpleNameResolver->getName($staticCall->class);
            if ($className === null) {
                return new MixedType();
            }

            return new ObjectType($className);
        }

        return $scope->getType($staticCall->class);
    }
}
