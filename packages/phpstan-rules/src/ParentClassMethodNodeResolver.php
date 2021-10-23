<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules;

use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use ReflectionMethod;
use Symplify\Astral\ReflectionParser;

final class ParentClassMethodNodeResolver
{
    public function __construct(
        private NodeFinder $nodeFinder,
        private ReflectionParser $reflectionParser,
    ) {
    }

    public function resolveParentClassMethod(Scope $scope, string $methodName): ?ClassMethod
    {
        /** @var ClassReflection[] $parentClassReflections */
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
     * @return Param[]
     */
    public function resolveParentClassMethodParams(Scope $scope, string $methodName): array
    {
        /** @var ClassReflection[] $parentClassReflections */
        $parentClassReflections = $this->getParentClassIncludeInterfaceReflections($scope);

        foreach ($parentClassReflections as $parentClassReflection) {
            $parentClassNodes = $this->reflectionParser->parseClassReflection($parentClassReflection);
            if ($parentClassNodes === []) {
                continue;
            }

            /** @var ClassLike[] $classes */
            $classes = $this->nodeFinder->findInstanceOf($parentClassNodes, ClassLike::class);
            if ($classes === []) {
                continue;
            }

            foreach ($classes as $class) {
                $classMethod = $class->getMethod($methodName);
                if (! $classMethod instanceof ClassMethod) {
                    continue;
                }

                return $classMethod->params;
            }
        }

        return [];
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

    /**
     * @return ClassReflection[]
     */
    private function getParentClassIncludeInterfaceReflections(Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        return array_merge($classReflection->getParents(), $classReflection->getInterfaces());
    }
}
