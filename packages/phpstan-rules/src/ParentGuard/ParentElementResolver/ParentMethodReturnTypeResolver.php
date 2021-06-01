<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\ParentGuard\ParentElementResolver;

use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\Php\PhpMethodReflection;
use PHPStan\Type\Type;

final class ParentMethodReturnTypeResolver
{
    public function resolve(Scope $scope): ?Type
    {
        $functionReflection = $scope->getFunction();
        if (! $functionReflection instanceof MethodReflection) {
            return null;
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        $methodName = $functionReflection->getName();

        /** @var ClassReflection[] $parentClassLikeReflections */
        $parentClassLikeReflections = array_merge($classReflection->getParents(), $classReflection->getInterfaces());

        foreach ($parentClassLikeReflections as $parentClassLikeReflection) {
            if (! $parentClassLikeReflection->hasMethod($methodName)) {
                continue;
            }

            $nativeMethodReflection = $parentClassLikeReflection->getNativeMethod($methodName);
            if (! $nativeMethodReflection instanceof PhpMethodReflection) {
                continue;
            }

            $parametersAcceptor = ParametersAcceptorSelector::selectSingle($nativeMethodReflection->getVariants());
            if (! $parametersAcceptor instanceof FunctionVariant) {
                continue;
            }

            return $parametersAcceptor->getReturnType();
        }

        return null;
    }
}
