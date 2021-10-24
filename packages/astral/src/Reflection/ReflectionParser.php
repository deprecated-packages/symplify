<?php

declare(strict_types=1);

namespace Symplify\Astral\Reflection;

use PhpParser\Lexer\Emulative;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\NodeFinder;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPStan\Parser\CachedParser;
use PHPStan\Parser\Parser;
use PHPStan\Parser\SimpleParser;
use ReflectionMethod;
use ReflectionProperty;
use Throwable;

final class ReflectionParser
{
    private Parser $parser;

    public function __construct(
        ParserFactory $parserFactory,
        private NodeFinder $nodeFinder
    ) {
        // @todo extract to DI factory and require by interface to allow re-use injection
        $lexerEmulative = new Emulative();
        $nativeParser = $parserFactory->create(ParserFactory::PREFER_PHP7, $lexerEmulative);

        $nameResolver = new NameResolver();
        $simpleParser = new SimpleParser($nativeParser, $nameResolver);

        $this->parser = new CachedParser($simpleParser, 1024);
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
