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

        $nativePropertyReflection = $classReflection->getNativeProperty($propertyName);
        return $nativePropertyReflection->isPublic();
    }

    public function hasPublicNativeMethod(ClassReflection $classReflection, string $getterMethodName): bool
    {
        if (! $classReflection->hasNativeMethod($getterMethodName)) {
            return false;
        }

        $methodReflection = $classReflection->getNativeMethod($getterMethodName);
        return $methodReflection->isPublic();
    }
}
