<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeAnalyzer;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Reflection\ResolvedMethodReflection;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Symplify\Astral\Naming\SimpleNameResolver;

final class ClassMethodReturnTypeAnalyzer
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function resolve(ClassMethod $classMethod, Scope $scope): null|Type
    {
        /** @var string $methodName */
        $methodName = $this->simpleNameResolver->getName($classMethod);

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        $objectType = new ObjectType($classReflection->getName());
        $methodReflection = $scope->getMethodReflection($objectType, $methodName);
        if (! $methodReflection instanceof ResolvedMethodReflection) {
            return null;
        }

        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants());
        return $parametersAcceptor->getReturnType();
    }
}
