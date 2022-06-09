<?php

declare(strict_types=1);

namespace Symplify\Astral\Reflection;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Symplify\Astral\PhpParser\SmartPhpParser;
use Throwable;

/**
 * @api
 */
final class ReflectionParser
{
    /**
     * @var array<string, Class_>
     */
    private array $classesByFilename = [];

    public function __construct(
        private SmartPhpParser $smartPhpParser,
        private NodeFinder $nodeFinder
    ) {
    }

    public function parsePHPStanMethodReflection(MethodReflection $methodReflection): ?ClassMethod
    {
        $classReflection = $methodReflection->getDeclaringClass();

        $fileName = $classReflection->getFileName();
        if ($fileName === null) {
            return null;
        }

        $class = $this->parseFilenameToClass($fileName);
        if (! $class instanceof Node) {
            return null;
        }

        return $class->getMethod($methodReflection->getName());
    }

    public function parseMethodReflection(ReflectionMethod|MethodReflection $reflectionMethod): ?ClassMethod
    {
        $class = $this->parseNativeClassReflection($reflectionMethod->getDeclaringClass());
        if (! $class instanceof Class_) {
            return null;
        }

        return $class->getMethod($reflectionMethod->getName());
    }

    public function parsePropertyReflection(ReflectionProperty $reflectionProperty): ?Property
    {
        $class = $this->parseNativeClassReflection($reflectionProperty->getDeclaringClass());
        if (! $class instanceof Class_) {
            return null;
        }

        return $class->getProperty($reflectionProperty->getName());
    }

    public function parseClassReflection(ClassReflection $classReflection): ?Class_
    {
        $filename = $classReflection->getFileName();
        if ($filename === null) {
            return null;
        }

        return $this->parseFilenameToClass($filename);
    }

    private function parseNativeClassReflection(ReflectionClass|ClassReflection $reflectionClass): ?Class_
    {
        $fileName = $reflectionClass->getFileName();
        if ($fileName === false) {
            return null;
        }

        if ($fileName === null) {
            return null;
        }

        return $this->parseFilenameToClass($fileName);
    }

    private function parseFilenameToClass(string $fileName): Class_|null
    {
        if (isset($this->classesByFilename[$fileName])) {
            return $this->classesByFilename[$fileName];
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

        $this->classesByFilename[$fileName] = $class;

        return $class;
    }
}
