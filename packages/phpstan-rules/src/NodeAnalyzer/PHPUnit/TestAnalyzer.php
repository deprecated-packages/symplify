<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\PHPUnit;

use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPUnit\Framework\TestCase;

final class TestAnalyzer
{
    public function isTestClassMethod(Scope $scope, MethodCall | ClassMethod | Function_ $node): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }
        if (! $node instanceof ClassMethod) {
            return $classReflection->isSubclassOf(TestCase::class);
        }
        if ($node->isPublic()) {
            return $classReflection->isSubclassOf(TestCase::class);
        }
        return false;
    }
}
