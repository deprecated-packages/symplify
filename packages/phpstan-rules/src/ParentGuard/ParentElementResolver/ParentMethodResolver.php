<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ParentGuard\ParentElementResolver;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpMethodReflection;

final class ParentMethodResolver
{
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
