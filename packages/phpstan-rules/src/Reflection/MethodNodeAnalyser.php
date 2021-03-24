<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use Symplify\PackageBuilder\ValueObject\MethodName;

final class MethodNodeAnalyser
{
    public function hasParentVendorLock(Scope $scope, string $methodName): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        foreach ($classReflection->getAncestors() as $ancestorClassReflection) {
            if ($classReflection === $ancestorClassReflection) {
                continue;
            }

            if ($ancestorClassReflection->hasNativeMethod($methodName)) {
                return true;
            }
        }

        return false;
    }

    public function isInConstructor(Scope $scope): bool
    {
        $reflectionFunction = $scope->getFunction();
        if (! $reflectionFunction instanceof MethodReflection) {
            return false;
        }

        return $reflectionFunction->getName() === MethodName::CONSTRUCTOR;
    }
}
