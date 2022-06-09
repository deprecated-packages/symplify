<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use PHPStan\Reflection\ClassReflection;

final class PublicClassReflectionAnalyzer
{
    public function hasPublicNativeProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        if (! $classReflection->hasNativeProperty($propertyName)) {
            return false;
        }

        $phpPropertyReflection = $classReflection->getNativeProperty($propertyName);
        return $phpPropertyReflection->isPublic();
    }

    public function hasPublicNativeMethod(ClassReflection $classReflection, string $getterMethodName): bool
    {
        if (! $classReflection->hasNativeMethod($getterMethodName)) {
            return false;
        }

        $extendedMethodReflection = $classReflection->getNativeMethod($getterMethodName);
        return $extendedMethodReflection->isPublic();
    }
}
