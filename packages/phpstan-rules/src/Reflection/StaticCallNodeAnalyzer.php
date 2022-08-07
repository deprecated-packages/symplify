<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

final class StaticCallNodeAnalyzer
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
    }

    /**
     * @api
     */
    public function isAbstractMethodStaticCall(Expr $expr, Scope $scope): bool
    {
        if (! $expr instanceof StaticCall) {
            return false;
        }

        $callerType = $this->resolveStaticCallCallerType($expr, $scope);

        if (! $expr->name instanceof Identifier) {
            return false;
        }

        $methodName = $expr->name->toString();

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
            $className = $staticCall->class->toString();
            return new ObjectType($className);
        }

        return $scope->getType($staticCall->class);
    }
}
