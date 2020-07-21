<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\Symfony\Type;

use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @inspiration https://github.com/phpstan/phpstan-symfony/blob/master/src/Type/Symfony/ServiceDynamicReturnTypeExtension.php
 */
final class ContainerGetTypeExtension implements DynamicMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return ContainerInterface::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'get';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        if (! isset($methodCall->args[0])) {
            return $returnType;
        }

        $className = $this->resolveClassName($methodCall->args[0]->value);
        if ($className !== null) {
            return new ObjectType($className);
        }

        return $returnType;
    }

    private function resolveClassName(Expr $expr): ?string
    {
        if ($expr instanceof ClassConstFetch) {
            if ($expr->class instanceof Name) {
                return $expr->class->toString();
            }

            return null;
        }

        return null;
    }
}
