<?php

declare(strict_types=1);

namespace Symplify\PHPStanExtensions\ReturnTypeExtension;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\StaticFactory\SimpleNameResolverStaticFactory;

/**
 * @inspiration https://github.com/phpstan/phpstan-symfony/blob/master/src/Type/Symfony/ServiceDynamicReturnTypeExtension.php
 */
final class ContainerGetReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct()
    {
        // intentionally manual here, to prevent double service registration caused by nette/di
        $this->simpleNameResolver = SimpleNameResolverStaticFactory::create();
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
        $returnType = ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        if (! isset($methodCall->args[0])) {
            return $returnType;
        }

        $className = $this->simpleNameResolver->getName($methodCall->args[0]->value);
        if ($className !== null) {
            return new ObjectType($className);
        }

        return $returnType;
    }
}
