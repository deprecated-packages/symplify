<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection\Parser;

use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PHPStan\Parser\Parser;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;
use Throwable;

final class ReflectionParser
{
    public function __construct(
        private Parser $parser,
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

    /**
     * @param ReflectionMethod|ReflectionProperty $reflector
     */
    private function parseReflectionToClass(Reflector $reflector): ?Class_
    {
        $reflectionClass = $reflector->getDeclaringClass();

        $fileName = $reflectionClass->getFileName();
        if ($fileName === false) {
            return null;
        }

        try {
            $nodes = $this->parser->parseFile($fileName);
        } catch (Throwable) {
            // not reachable
            return null;
        }

        $class = $this->nodeFinder->findFirstInstanceOf($nodes, Class_::class);
        if (! $class instanceof Class_) {
            return null;
        }

        return $class;
    }
}
