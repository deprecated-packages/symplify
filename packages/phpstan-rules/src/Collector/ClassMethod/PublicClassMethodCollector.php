<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Collector\ClassMethod;

use Twig\Extension\ExtensionInterface;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;
use PHPStan\Reflection\ClassReflection;
use Symplify\PHPStanRules\Matcher\Collector\PublicClassMethodMatcher;
use Symplify\PHPStanRules\PhpDoc\ApiDocStmtAnalyzer;

/**
 * @implements Collector<ClassMethod, array{class-string, string, int}|null>
 */
final class PublicClassMethodCollector implements Collector
{
    public function __construct(
        private ApiDocStmtAnalyzer $apiDocStmtAnalyzer,
        private PublicClassMethodMatcher $publicClassMethodMatcher,
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
        $classReflection = $scope->getClassReflection();

        // skip
        if ($classReflection instanceof ClassReflection && $classReflection->isSubclassOf(
            ExtensionInterface::class
        )) {
            return null;
        }

        if ($this->publicClassMethodMatcher->shouldSkipClassMethod($node)) {
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

        if ($this->publicClassMethodMatcher->shouldSkipClassReflection($classReflection)) {
            return null;
        }

        $methodName = $node->name->toString();

        // is this method required by parent contract? skip it
        if ($this->publicClassMethodMatcher->isUsedByParentClassOrInterface($classReflection, $methodName)) {
            return null;
        }

        return [$classReflection->getName(), $methodName, $node->getLine()];
    }
}
