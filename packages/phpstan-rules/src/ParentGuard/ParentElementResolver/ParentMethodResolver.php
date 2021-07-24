<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ParentGuard\ParentElementResolver;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpMethodReflection;
use Symplify\Astral\Naming\SimpleNameResolver;

final class ParentMethodResolver
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function resolveFromClassMethod(Scope $scope, ClassMethod $classMethod): ?PhpMethodReflection
    {
        /** @var string $methodName */
        $methodName = $this->simpleNameResolver->getName($classMethod);
        return $this->resolve($scope, $methodName);
    }

    public function resolve(Scope $scope, string $methodName): ?PhpMethodReflection
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        /** @var ClassReflection[] $parentClassLikeReflections */
        $parentClassLikeReflections = array_merge($classReflection->getParents(), $classReflection->getInterfaces());

        foreach ($parentClassLikeReflections as $parentClassLikeReflection) {
            // this is needed, as PHPStan takes parent @method anontation as real method
            if (! $parentClassLikeReflection->hasNativeMethod($methodName)) {
                continue;
            }

            $nativeMethodReflection = $parentClassLikeReflection->getNativeMethod($methodName);
            if (! $nativeMethodReflection instanceof PhpMethodReflection) {
                continue;
            }

            return $nativeMethodReflection;
        }

        return null;
    }
}
