<?php

declare(strict_types=1);

namespace Symplify\PHPStanLatteRules\NodeAnalyzer;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\Type;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class ComponentClassMethodTypeAnalyzer
{
    public function resolveReturnType(ClassMethod $classMethod, Scope $scope): Type
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            throw new ShouldNotHappenException();
        }

        $methodName = (string) $classMethod->name;
        $methodReflection = $classReflection->getNativeMethod($methodName);

        $parametersAcceptor = $methodReflection->getVariants()[0];
        return $parametersAcceptor->getReturnType();
    }
}
