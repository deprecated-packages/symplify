<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;

final class ParentMethodAnalyser
{
    public function hasParentClassMethodWithSameName(Scope $scope, string $methodName): bool
    {
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return false;
        }

        /** @var ClassReflection[] $parentClassLikeReflections */
        $parentClassLikeReflections = array_merge($classReflection->getParents(), $classReflection->getInterfaces());

        foreach ($parentClassLikeReflections as $classLikeReflection) {
            if ($classLikeReflection->hasMethod($methodName)) {
                return true;
            }
        }

        return false;
    }
}
