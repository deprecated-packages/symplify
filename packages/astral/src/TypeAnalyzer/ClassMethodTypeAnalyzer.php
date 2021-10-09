<?php

declare(strict_types=1);

namespace Symplify\Astral\TypeAnalyzer;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Type\Type;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;

final class ClassMethodTypeAnalyzer
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

    /**
     * @param string[] $methodNames
     */
    public function isClassMethodOfNamesAndType(
        ClassMethod $classMethod,
        Scope $scope,
        array $methodNames,
        string $classType
    ): bool {
        $classMethodName = (string) $classMethod->name;
        if (! in_array($classMethodName, $methodNames, true)) {
            return false;
        }

        return $this->isClassType($scope, $classType);
    }

    private function isClassType(Scope $scope, string $classType): bool
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return false;
        }

        if ($classReflection->isSubclassOf($classType)) {
            return true;
        }

        return $classReflection->hasTraitUse($classType);
    }
}
