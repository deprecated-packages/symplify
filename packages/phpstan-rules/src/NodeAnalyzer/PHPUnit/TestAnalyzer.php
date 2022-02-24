<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\PHPUnit;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPUnit\Framework\TestCase;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;

final class TestAnalyzer
{
    public function __construct(
        private SimpleNodeFinder $simpleNodeFinder
    ) {
    }

    public function isTestClassMethod(Scope $scope, FunctionLike $functionLike): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return $this->isPublicClassMethod($functionLike, $classReflection);
    }

    public function isInTestClassMethod(Scope $scope, Node $node): bool
    {
        $classMethod = $this->simpleNodeFinder->findFirstParentByType($node, ClassMethod::class);
        if (! $classMethod instanceof ClassMethod) {
            return false;
        }

        return $this->isTestClassMethod($scope, $classMethod);
    }

    public function isInTest(Scope $scope): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return $classReflection->isSubclassOf('PHPUnit\Framework\TestCase');
    }

    private function isPublicClassMethod(FunctionLike $functionLike, ClassReflection $classReflection): bool
    {
        if (! $functionLike instanceof ClassMethod) {
            return false;
        }

        if (! $functionLike->isPublic()) {
            return false;
        }

        return $classReflection->isSubclassOf(TestCase::class);
    }
}
