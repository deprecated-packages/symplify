<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use ReflectionMethod;
use Symplify\Astral\Reflection\ReflectionParser;

final class ParentClassMethodNodeResolver
{
    public function __construct(
        private ReflectionParser $reflectionParser
    ) {
    }

    public function resolveParentClassMethod(Scope $scope, string $methodName): ?ClassMethod
    {
        $parentClassReflections = $this->getParentClassReflections($scope);

        foreach ($parentClassReflections as $parentClassReflection) {
            if (! $parentClassReflection->hasMethod($methodName)) {
                continue;
            }

            $parentMethodReflection = new ReflectionMethod($parentClassReflection->getName(), $methodName);
            return $this->reflectionParser->parseMethodReflection($parentMethodReflection);
        }

        return null;
    }

    /**
     * @return ClassReflection[]
     */
    private function getParentClassReflections(Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        return $classReflection->getParents();
    }
}
