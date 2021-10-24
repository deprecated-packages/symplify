<?php

declare(strict_types=1);

namespace Symplify\Astral;

use ReflectionClass;
use ReflectionProperty;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PhpParser\Parser;
use PhpParser\ParserFactory;
use PHPStan\Reflection\ClassReflection;
use ReflectionMethod;
use Symplify\SmartFileSystem\SmartFileSystem;

final class ReflectionParser
{
    private Parser $parser;

    public function __construct(
        private SmartFileSystem $smartFileSystem,
        private NodeFinder $nodeFinder,
        ParserFactory $parserFactory
    ) {
        $this->parser = $parserFactory->create(ParserFactory::PREFER_PHP7);
    }

    /**
     * @return Stmt[]
     */
    public function parseClassReflection(ClassReflection|ReflectionClass $classReflection): array
    {
        $fileName = $classReflection->getFileName();
        if ($fileName === false) {
            return [];
        }

        $fileContents = $this->smartFileSystem->readFile($fileName);

        $stmts = $this->parser->parse($fileContents);
        if (is_array($stmts)) {
            return $stmts;
        }

        return [];
    }

    public function parseMethodReflection(ReflectionMethod $reflectionMethod): ?ClassMethod
    {
        $class = $this->parseReflectionToClass($reflectionMethod);
        if (! $class instanceof Class_) {
            return null;
        }

        return $class->getMethod($reflectionMethod->getName());
    }

    public function parsePropertyReflection(ReflectionProperty $reflectionProperty): ?Property
    {
        $class = $this->parseReflectionToClass($reflectionProperty);
        if (! $class instanceof Class_) {
            return null;
        }

        return $class->getProperty($reflectionProperty->getName());
    }

    private function parseReflectionToClass(ReflectionMethod | ReflectionProperty $reflector): ?Class_
    {
        $reflectionClass = $reflector->getDeclaringClass();

        $stmts = $this->parseClassReflection($reflectionClass);

//        $fileName = $reflectionClass->getFileName();
//        if ($fileName === false) {
//            return null;
//        }
//
//        try {
//            $nodes = $this->parser->parse($fileName);
//        } catch (\Throwable) {
//            // not reachable
//            return null;
//        }

        $class = $this->nodeFinder->findFirstInstanceOf($stmts, Class_::class);
        if (! $class instanceof Class_) {
            return null;
        }

        return $class;
    }
}
