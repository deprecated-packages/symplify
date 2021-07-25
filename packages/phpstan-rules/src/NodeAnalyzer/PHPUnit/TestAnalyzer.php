<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\PHPUnit;

use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPUnit\Framework\TestCase;

final class TestAnalyzer
{
    public function isTestClassMethod(Scope $scope, ClassMethod | Function_ $node): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        if (! $node instanceof ClassMethod) {
            return false;
        }

        if (! $node->isPublic()) {
            return false;
        }

        return $classReflection->isSubclassOf(TestCase::class);
    }
}
