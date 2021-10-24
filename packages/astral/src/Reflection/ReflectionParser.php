<?php

declare(strict_types=1);

namespace Symplify\Astral\Reflection;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use ReflectionMethod;
use ReflectionProperty;
use Symplify\Astral\PhpParser\SmartPhpParser;
use Throwable;

/**
 * @api
 */
final class ReflectionParser
{
    public function __construct(
        private SmartPhpParser $smartPhpParser,
        private NodeFinder $nodeFinder
    ) {
    }

    public function parseMethodReflectionToClassMethod(ReflectionMethod $reflectionMethod): ?ClassMethod
    {
        $class = $this->parseReflectionToClass($reflectionMethod);
        if (! $class instanceof Class_) {
            return null;
        }

        return $class->getMethod($reflectionMethod->getName());
    }

    public function parsePropertyReflectionToProperty(ReflectionProperty $reflectionProperty): ?Property
    {
        $class = $this->parseReflectionToClass($reflectionProperty);
        if (! $class instanceof Class_) {
            return null;
        }

        return $class->getProperty($reflectionProperty->getName());
    }

    private function parseReflectionToClass(\ReflectionMethod | \ReflectionProperty $reflector): ?Class_
    {
        $reflectionClass = $reflector->getDeclaringClass();

        $fileName = $reflectionClass->getFileName();
        if ($fileName === false) {
            return null;
        }

        try {
            $stmts = $this->smartPhpParser->parseFile($fileName);
        } catch (Throwable) {
            // not reachable
            return null;
        }

        $class = $this->nodeFinder->findFirstInstanceOf($stmts, Class_::class);
        if (! $class instanceof Class_) {
            return null;
        }

        return $class;
    }
}
