<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ParentGuard;

use PhpParser\Node\Stmt\Property;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;

final class ParentPropertyGuard
{
    public function isPropertyGuarded(Property $property, Scope $scope): bool
    {
        $propertyProperty = $property->props[0];
        $propertyName = $propertyProperty->name->toString();

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        foreach ($classReflection->getParents() as $parentClassReflectoin) {
            if (! $parentClassReflectoin->hasNativeProperty($propertyName)) {
                continue;
            }

            return true;
        }

        return false;
    }
}
