<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\ReturnTypeExtension;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\PHPStanExtensions\TypeResolver\ClassConstFetchReturnTypeResolver;

/**
 * @inspiration https://github.com/phpstan/phpstan-symfony/blob/master/src/Type/Symfony/ServiceDynamicReturnTypeExtension.php
 */
final class ContainerGetReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    private ClassConstFetchReturnTypeResolver $classConstFetchReturnTypeResolver;

    public function __construct()
    {
        $this->classConstFetchReturnTypeResolver = new ClassConstFetchReturnTypeResolver();
    }

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
        return $this->classConstFetchReturnTypeResolver->resolve($methodReflection, $methodCall);
    }
}
