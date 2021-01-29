<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use ReflectionMethod;
use Symplify\SmartFileSystem\SmartFileSystem;
use Throwable;

final class ParentClassMethodNodeResolver
{
    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var Parser
     */
    private $phpParser;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    public function __construct(SmartFileSystem $smartFileSystem, Parser $phpParser, NodeFinder $nodeFinder)
    {
        $this->smartFileSystem = $smartFileSystem;
        $this->phpParser = $phpParser;
        $this->nodeFinder = $nodeFinder;
    }

    /**
     * @return Node[]
     */
    public function resolveParentClassMethodNodes(Scope $scope, string $methodName): array
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
                $parentClassNodes = $this->parseFileToNodes($fileName);
            } catch (Throwable $throwable) {
                // not reachable
                return [];
            }

            $class = $this->nodeFinder->findFirstInstanceOf($parentClassNodes, Class_::class);
            if (! $class instanceof Class_) {
                return [];
            }

            $classMethod = $class->getMethod($methodName);
            if (! $classMethod instanceof ClassMethod) {
                continue;
            }

            return (array) $classMethod->getStmts();
        }

        return [];
    }

    /**
     * @return Param[]
     */
    public function resolveParentClassMethodParams(Scope $scope, string $methodName): array
    {
        /** @var ClassReflection[] $parentClassReflections */
        $parentClassReflections = $this->getParentClassIncludeInterfaceReflections($scope);
        foreach ($parentClassReflections as $parentClassReflection) {
            try {
                $parentClassNodes = $this->parseFileToNodes((string) $parentClassReflection->getFileName());
            } catch (Throwable $throwable) {
                // not reachable
                return [];
            }

            /** @var ClassLike[] $classes */
            $classes = $this->nodeFinder->findInstanceOf($parentClassNodes, ClassLike::class);
            if ($classes === []) {
                return [];
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
    private function parseFileToNodes(string $filePath): array
    {
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return (array) $this->phpParser->parse($fileContent);
    }
}
