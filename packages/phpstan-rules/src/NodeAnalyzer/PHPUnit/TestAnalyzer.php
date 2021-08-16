<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\PHPUnit;

use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
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

    public function isTestClassMethod(Scope $scope, MethodCall | ClassMethod | Function_ $node): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return $this->isPublicClassMethod($node, $classReflection);
    }

    public function isInTestClassMethod(Scope $scope, Node $node): bool
    {
        $classMethod = $this->simpleNodeFinder->findFirstParentByType($node, ClassMethod::class);
        if (! $classMethod instanceof ClassMethod) {
            return false;
        }

        return $this->isTestClassMethod($scope, $classMethod);
    }

    private function isPublicClassMethod(ClassMethod|Function_|MethodCall $node, ClassReflection $classReflection): bool
    {
        if (! $node instanceof ClassMethod) {
            return false;
        }

        if (! $node->isPublic()) {
            return false;
        }

        return $classReflection->isSubclassOf(TestCase::class);
    }
}
