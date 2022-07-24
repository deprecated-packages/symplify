<?php

declare(strict_types=1);

namespace Symplify\Astral\TypeAnalyzer;

use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;
use Symplify\Astral\Exception\ShouldNotHappenException;
use Symplify\Astral\Naming\SimpleNameResolver;

/**
 * @api
 */
final class ClassMethodReturnTypeResolver
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function resolve(ClassMethod $classMethod, Scope $scope): Type
    {
        $methodName = $this->simpleNameResolver->getName($classMethod);
        if (! is_string($methodName)) {
            throw new ShouldNotHappenException();
        }

        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return new MixedType();
        }

        $extendedMethodReflection = $classReflection->getMethod($methodName, $scope);

        $parametersAcceptor = ParametersAcceptorSelector::selectSingle($extendedMethodReflection->getVariants());
        if (! $parametersAcceptor instanceof FunctionVariant) {
            return new MixedType();
        }

        return $parametersAcceptor->getReturnType();
    }
}
