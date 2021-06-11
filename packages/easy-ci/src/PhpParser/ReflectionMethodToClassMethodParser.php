<?php

declare(strict_types=1);

namespace Symplify\EasyCI\PhpParser;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use ReflectionMethod;
use Symplify\SmartFileSystem\SmartFileSystem;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ReflectionMethodToClassMethodParser
{
    public function __construct(
        private Parser $phpParser,
        private NodeFinder $nodeFinder,
        private SmartFileSystem $smartFileSystem
    ) {
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

        $classMethod = $this->nodeFinder->findFirst($nodes, static function (Node $node) use (
            $desiredMethodName
        ): bool {
            if (! $node instanceof ClassMethod) {
                return false;
            }

            return (string) $node->name === $desiredMethodName;
        });

        if (! $classMethod instanceof ClassMethod) {
            $reflectionClass = $reflectionMethod->getDeclaringClass();
            $className = $reflectionClass->getName();

            $errorMessage = sprintf('Method "%s" could not found in "%s" class', $desiredMethodName, $className);
            throw new ShouldNotHappenException($errorMessage);
        }

        return $classMethod;
    }
}
