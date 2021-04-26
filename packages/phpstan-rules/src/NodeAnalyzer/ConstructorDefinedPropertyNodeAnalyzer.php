<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\PropertyFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpClassReflectionExtension;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;

final class ConstructorDefinedPropertyNodeAnalyzer
{
    /**
     * @var PhpClassReflectionExtension
     */
    private $phpClassReflectionExtension;

    /**
     * @var PrivatesCaller
     */
    private $privatesCaller;

    /**
     * @var SimpleNameResolver
     */
    private $simpleNameResolver;

    public function __construct(
        PhpClassReflectionExtension $phpClassReflectionExtension,
        PrivatesCaller $privatesCaller,
        SimpleNameResolver $simpleNameResolver
    ) {
        $this->phpClassReflectionExtension = $phpClassReflectionExtension;
        $this->privatesCaller = $privatesCaller;
        $this->simpleNameResolver = $simpleNameResolver;
    }

    public function isLocalPropertyDefinedInConstructor(PropertyFetch $propertyFetch, Scope $scope): bool
    {
        if (! $this->simpleNameResolver->isName($propertyFetch->var, 'this')) {
            return false;
        }

        $dependencyPropertyNames = $this->resolvePropertyNames($scope);

        $propertyName = $this->simpleNameResolver->getName($propertyFetch->name);
        return in_array($propertyName, $dependencyPropertyNames, true);
    }

    /**
     * @return string[]
     */
    private function resolvePropertyNames(Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        if (! $classReflection->hasConstructor()) {
            return [];
        }

        $constructorMethodReflection = $classReflection->getConstructor();

        $propertyNameToTypes = $this->privatesCaller->callPrivateMethod(
            $this->phpClassReflectionExtension,
            'inferAndCachePropertyTypes',
            [$constructorMethodReflection]
        );

        $propertyNames = [];
        foreach (array_keys($propertyNameToTypes) as $propertyName) {
            if (! is_string($propertyName)) {
                continue;
            }

            $propertyNames[] = $propertyName;
        }

        return $propertyNames;
    }
}
