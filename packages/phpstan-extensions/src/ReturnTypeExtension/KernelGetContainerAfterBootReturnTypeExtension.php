<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\ReturnTypeExtension;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use Symfony\Component\HttpKernel\Kernel;
use Symplify\PHPStanExtensions\TypeResolver\ClassConstFetchReturnTypeResolver;

final class KernelGetContainerAfterBootReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /**
     * @var ClassConstFetchReturnTypeResolver
     */
    private $classConstFetchReturnTypeResolver;

    public function __construct(ClassConstFetchReturnTypeResolver $classConstFetchReturnTypeResolver)
    {
        $this->classConstFetchReturnTypeResolver = $classConstFetchReturnTypeResolver;
    }

    public function getClass(): string
    {
        return Kernel::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'getContainer';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        return $this->classConstFetchReturnTypeResolver->resolve($methodReflection, $methodCall);
    }
}
