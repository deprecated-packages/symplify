<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\New_;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpClassReflectionExtension;
use PHPStan\Type\Type;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\Astral\NodeFinder\SimpleNodeFinder;
use Symplify\PackageBuilder\Reflection\PrivatesCaller;
use Symplify\PackageBuilder\ValueObject\MethodName;

final class ConstructorDefinedPropertyNodeAnalyzer
{
    public function __construct(
        private PhpClassReflectionExtension $phpClassReflectionExtension,
        private PrivatesCaller $privatesCaller,
        private SimpleNameResolver $simpleNameResolver,
        private SimpleNodeFinder $simpleNodeFinder
    ) {
    }

    public function isLocalPropertyDefinedInConstructor(PropertyFetch $propertyFetch, Scope $scope): bool
    {
        if (! $this->simpleNameResolver->isName($propertyFetch->var, 'this')) {
            return false;
        }

        $dependencyPropertyNames = $this->resolvePropertyNames($scope, $propertyFetch);

        $propertyName = $this->simpleNameResolver->getName($propertyFetch->name);
        return in_array($propertyName, $dependencyPropertyNames, true);
    }

    /**
     * @return string[]
     */
    private function resolvePropertyNames(Scope $scope, PropertyFetch $propertyFetch): array
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return [];
        }

        $propertyNames = [];

        $propertyNameToTypes = $this->resolveConstructorPropertyNamesToTypes($classReflection);
        foreach (array_keys($propertyNameToTypes) as $propertyName) {
            if (! is_string($propertyName)) {
                continue;
            }

            $propertyNames[] = $propertyName;
        }

        $justCreatedPropertyNames = $this->resolveConstructorJustCreatedPropertyNames($propertyFetch);

        return array_diff($propertyNames, $justCreatedPropertyNames);
    }

    /**
     * Returns just created constructor properties, e.g. $this->value = new SomeObject();
     *
     * @return string[]
     */
    private function resolveConstructorJustCreatedPropertyNames(PropertyFetch $propertyFetch): array
    {
        $justCreatedPropertyNames = [];

        $class = $this->simpleNodeFinder->findFirstParentByType($propertyFetch, Class_::class);
        if (! $class instanceof Class_) {
            return [];
        }

        $constructorClassMethod = $class->getMethod(MethodName::CONSTRUCTOR);
        if (! $constructorClassMethod instanceof ClassMethod) {
            return [];
        }

        $assigns = $this->simpleNodeFinder->findByType($constructorClassMethod, Assign::class);
        foreach ($assigns as $assign) {
            if (! $assign->var instanceof PropertyFetch) {
                continue;
            }

            $propertyFetch = $assign->var;
            if (! $this->simpleNameResolver->isName($propertyFetch->var, 'this')) {
                continue;
            }

            if (! $assign->expr instanceof New_) {
                continue;
            }

            $propertyFetchName = $this->simpleNameResolver->getName($propertyFetch->name);
            if ($propertyFetchName === null) {
                continue;
            }

            $justCreatedPropertyNames[] = $propertyFetchName;
        }

        return $justCreatedPropertyNames;
    }

    /**
     * @return array<string, Type>
     */
    private function resolveConstructorPropertyNamesToTypes(ClassReflection $classReflection): array
    {
        if (! $classReflection->hasConstructor()) {
            return [];
        }

        $constructorMethodReflection = $classReflection->getConstructor();

        return $this->privatesCaller->callPrivateMethod(
            $this->phpClassReflectionExtension,
            'inferAndCachePropertyTypes',
            [$constructorMethodReflection]
        );
    }
}
