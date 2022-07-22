<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ParentGuard;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\Php\PhpMethodReflection;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\ParentGuard\ParentElementResolver\ParentMethodResolver;

final class ParentClassMethodGuard
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver,
        private ParentMethodResolver $parentMethodResolver
    ) {
    }

    public function isClassMethodGuardedByParentClassMethod(ClassMethod $classMethod, Scope $scope): bool
    {
        $classMethodName = $this->simpleNameResolver->getName($classMethod);
        if ($classMethodName === null) {
            return false;
        }

        $phpMethodReflection = $this->parentMethodResolver->resolve($scope, $classMethodName);
        return $phpMethodReflection instanceof PhpMethodReflection;
    }
}
