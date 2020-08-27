<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\PHPStan;

use PHPStan\Analyser\Scope;

final class ParentMethodAnalyser
{
    public function hasParentClassMethodWithSameName(Scope $scope, string $methodName): bool
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }

        foreach ($classReflection->getParents() as $parentClass) {
            if ($parentClass->hasMethod($methodName)) {
                return true;
            }
        }

        foreach ($classReflection->getInterfaces() as $interface) {
            if ($interface->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
