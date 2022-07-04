<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassMethod;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\PhpDoc\ApiDocStmtAnalyzer;

/**
 * @implements Collector<ClassMethod, array{class-string, string, int}|null>
 */
final class PublicClassMethodCollector implements Collector
{
    public function __construct(
        private ApiDocStmtAnalyzer $apiDocStmtAnalyzer
    ) {
    }

    public function getNodeType(): string
    {
        return ClassMethod::class;
    }

    /**
     * @param ClassMethod $node
     * @return array<array{class-string, string, int}>|null
     */
    public function processNode(Node $node, Scope $scope): ?array
    {
        if ($this->shouldSkipClassMethod($node)) {
            return null;
        }

        // only if the class has no parents/implementers, to avoid class method required by contracts
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        if ($this->apiDocStmtAnalyzer->isApiDoc($node, $classReflection)) {
            return null;
        }

        // skip interface as required, traits as unable to detect for sure
        if (! $classReflection->isClass()) {
            return null;
        }

        if ($classReflection->getParents() !== []) {
            return null;
        }

        $methodName = $node->name->toString();

        // is this method required by parent contract? skip it
        foreach ($classReflection->getInterfaces() as $parentInterfaceReflection) {
            if ($parentInterfaceReflection->hasMethod($methodName)) {
                return null;
            }
<<<<<<< HEAD
        }

        return [$classReflection->getName(), $methodName, $node->getLine()];
    }

    private function shouldSkipClassMethod(ClassMethod $classMethod): bool
    {
        if ($classMethod->isMagic()) {
            return true;
        }

        if ($classMethod->isStatic()) {
            return true;
=======
>>>>>>> [PHPStanRules] Check interface methods over the contract
        }
//
//        if ($classReflection->getInterfaces() !== []) {
//            return null;
//        }

<<<<<<< HEAD
        return ! $classMethod->isPublic();
=======
        return [$classReflection->getName(), $methodName, $node->getLine()];
>>>>>>> [PHPStanRules] Check interface methods over the contract
    }
}
