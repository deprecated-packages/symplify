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

    public function hasPublicNativeMethod(ClassReflection $callerClassReflection, string $getterMethodName): bool
    {
        if (! $callerClassReflection->hasNativeMethod($getterMethodName)) {
            return false;
        }

        $methodReflection = $callerClassReflection->getNativeMethod($getterMethodName);
        return $methodReflection->isPublic();
    }
}
