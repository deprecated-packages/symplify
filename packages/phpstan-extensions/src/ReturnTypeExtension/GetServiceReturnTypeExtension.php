<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\ReturnTypeExtension;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\Type;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PHPStanExtensions\TypeResolver\ClassConstFetchReturnTypeResolver;

/**
 * Covers case:
 * \Symplify\PackageBuilder\Testing\AbstractKernelTestCase::getService($type)
 *
 * ↓
 * return object of: $type
 */
final class GetServiceReturnTypeExtension implements DynamicMethodReturnTypeExtension
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
        return AbstractKernelTestCase::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection): bool
    {
        return $methodReflection->getName() === 'getService';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ): Type {
        return $this->classConstFetchReturnTypeResolver->resolve($methodReflection, $methodCall);
    }
}
