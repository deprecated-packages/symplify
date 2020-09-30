<?php

declare(strict_types=1);

namespace Symplify\CodingStandard\PHPStan\NodeResolver;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
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
        /** @var ClassReflection $classReflection */
        $classReflection = $scope->getClassReflection();

        $parentClassReflection = $classReflection->getParentClass();
        if ($parentClassReflection === false) {
            return [];
        }

        $parentClassNodes = $this->parseFileToNodes((string) $parentClassReflection->getFileName());

        /** @var Class_|null $class */
        $class = $this->nodeFinder->findFirstInstanceOf($parentClassNodes, Class_::class);
        if ($class === null) {
            return [];
        }

        $classMethod = $class->getMethod($methodName);
        if ($classMethod === null) {
            return [];
        }

        return (array) $classMethod->getStmts();
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
