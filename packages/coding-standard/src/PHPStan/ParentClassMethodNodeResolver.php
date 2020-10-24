<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use Symplify\SmartFileSystem\SmartFileSystem;

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
            $parentClassNodes = $this->parseFileToNodes((string) $parentClassReflection->getFileName());

            /** @var Class_|null $class */
            $class = $this->nodeFinder->findFirstInstanceOf($parentClassNodes, Class_::class);
            if ($class === null) {
                return [];
            }

            $classMethod = $class->getMethod($methodName);
            if ($classMethod === null) {
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
            $parentClassNodes = $this->parseFileToNodes((string) $parentClassReflection->getFileName());

            /** @var ClassLike[] $classes */
            $classes = $this->nodeFinder->findInstanceOf($parentClassNodes, ClassLike::class);
            if ($classes === []) {
                return [];
            }

            foreach ($classes as $class) {
                $classMethod = $class->getMethod($methodName);
                if ($classMethod === null) {
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
        /** @var ClassReflection|null $classReflection */
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
            return [];
        }

        return $classReflection->getParents();
    }

    /**
     * @return ClassReflection[]
     */
    private function getParentClassIncludeInterfaceReflections(Scope $scope): array
    {
        /** @var ClassReflection|null $classReflection */
        $classReflection = $scope->getClassReflection();
        if ($classReflection === null) {
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
