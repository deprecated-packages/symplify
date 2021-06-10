<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\Reflection;

use PhpParser\Node\Expr\PropertyFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Type\TypeWithClassName;
use Symplify\Astral\Naming\SimpleNameResolver;

final class PropertyAnalyzer
{
    /**
     * @var ReflectionProvider
     */
    private $reflectionProvider;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(ReflectionProvider $reflectionProvider, SimpleNameResolver $simpleNameResolver)
    {
        $this->reflectionProvider = $reflectionProvider;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    public function matchPropertyReflection(PropertyFetch $propertyFetch, Scope $scope): ?PropertyReflection
    {
        $propertyFetchVarType = $scope->getType($propertyFetch->var);
        if (! $propertyFetchVarType instanceof TypeWithClassName) {
            return null;
        }

        $className = $propertyFetchVarType->getClassName();
        if (! $this->reflectionProvider->hasClass($className)) {
            return null;
        }

        $classReflection = $this->reflectionProvider->getClass($className);

        $propertyName = $this->simpleNameResolver->getName($propertyFetch->name);
        if ($propertyName === null) {
            return null;
        }

        if (! $classReflection->hasProperty($propertyName)) {
            return null;
        }

        return $classReflection->getProperty($propertyName, $scope);
    }
}
