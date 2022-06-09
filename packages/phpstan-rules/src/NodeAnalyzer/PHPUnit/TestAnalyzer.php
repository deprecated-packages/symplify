<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer\PHPUnit;

use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPUnit\Framework\TestCase;

final class TestAnalyzer
{
    public function isTestClassMethod(Scope $scope, FunctionLike $functionLike): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        return $this->isPublicClassMethod($functionLike, $classReflection);
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
