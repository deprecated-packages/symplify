<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PHPStan\Analyser\Scope;
use PHPStan\Parser\Parser;
use PHPStan\Reflection\ClassReflection;
use ReflectionMethod;
use Throwable;

final class ParentClassMethodNodeResolver
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(Parser $parser, NodeFinder $nodeFinder)
    {
        $this->parser = $parser;
        $this->nodeFinder = $nodeFinder;
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
            $fileName = $parentMethodReflection->getFileName();
            if ($fileName === false) {
                continue;
            }

            try {
                $parentClassNodes = $this->parser->parseFile($fileName);
            } catch (Throwable $throwable) {
                // not reachable
                return null;
            }

            $class = $this->nodeFinder->findFirstInstanceOf($parentClassNodes, Class_::class);
            if (! $class instanceof Class_) {
                return null;
            }

            return $class->getMethod($methodName);
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
            $parentClassNodes = $this->resolveClassNodes($parentClassReflection);
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

    /**
     * @return Node[]
     */
    private function resolveClassNodes(ClassReflection $parentClassReflection): array
    {
        try {
            $parentClassFileName = $parentClassReflection->getFileName();
            if ($parentClassFileName === false) {
                return [];
            }

            return $this->parser->parseFile($parentClassFileName);
        } catch (Throwable $throwable) {
            // not reachable
            return [];
        }
    }
}
