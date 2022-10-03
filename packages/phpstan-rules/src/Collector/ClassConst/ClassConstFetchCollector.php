<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassConst;

use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ClassReflection;

/**
 * @implements Collector<ClassConstFetch, string[]>
 */
final class ClassConstFetchCollector implements Collector
{
    public function getNodeType(): string
    {
        return ClassConstFetch::class;
    }

    /**
     * @param ClassConstFetch $node
     * @return string[]|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if (! $node->class instanceof Name) {
            return null;
        }

        if (! $node->name instanceof Identifier) {
            return null;
        }

        $className = $node->class->toString();
        $constantName = $node->name->toString();

        $classReflection = $scope->getClassReflection();
        if ($classReflection !== null && $classReflection->hasConstant($constantName)) {
            $constantReflection = $classReflection->getConstant($constantName);
            $declaringClass = $constantReflection->getDeclaringClass();
            if ($declaringClass->getFileName() !== $classReflection->getFileName()) {
                return [$declaringClass->getName(). '::' . $constantName];
            }
            return null;
        }

        return [$className . '::' . $constantName];
    }
}
