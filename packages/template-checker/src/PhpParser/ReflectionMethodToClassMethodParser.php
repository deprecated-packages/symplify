<?php

declare(strict_types=1);

namespace Symplify\TemplateChecker\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use ReflectionMethod;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ReflectionMethodToClassMethodParser
{
    /**
     * @var Parser
     */
    private $phpParser;

    /**
     * @var NodeFinder
     */
    private $nodeFinder;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    public function __construct(Parser $phpParser, NodeFinder $nodeFinder, SmartFileSystem $smartFileSystem)
    {
        $this->phpParser = $phpParser;
        $this->nodeFinder = $nodeFinder;
        $this->smartFileSystem = $smartFileSystem;
    }

    public function parse(ReflectionMethod $reflectionMethod): ClassMethod
    {
        $desiredMethodName = $reflectionMethod->name;

        $fileName = $reflectionMethod->getFileName();
        if ($fileName === false) {
            throw new ShouldNotHappenException();
        }

        $reflectionMethodFileContent = $this->smartFileSystem->readFile($fileName);
        $nodes = $this->phpParser->parse($reflectionMethodFileContent);
        if ($nodes === [] || $nodes === null) {
            throw new ShouldNotHappenException();
        }

        /** @var ClassMethod|null $classMethod */
        $classMethod = $this->nodeFinder->findFirst($nodes, static function (Node $node) use (
            $desiredMethodName
        ): bool {
            if (! $node instanceof ClassMethod) {
                return false;
            }

            return (string) $node->name === $desiredMethodName;
        });

        if ($classMethod === null) {
            $reflectionClass = $reflectionMethod->getDeclaringClass();
            $className = $reflectionClass->getName();

            $errorMessage = sprintf('Method "%s" could not found in "%s" class', $desiredMethodName, $className);
            throw new ShouldNotHappenException($errorMessage);
        }

        return $classMethod;
    }
}
