<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\TypeResolver;

use PhpParser\Node\Expr\PropertyFetch;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use PHPStan\Type\ArrayType;
use PHPStan\Type\BooleanType;
use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use Symplify\Astral\Naming\SimpleNameResolver;
use Symplify\PHPStanRules\Exception\UnknownValueTypeException;

final class NativePropertyFetchTypeResolver
{
    public function __construct(
        private SimpleNameResolver $simpleNameResolver
    ) {
    }

    public function resolve(PropertyFetch $propertyFetch, Scope $scope): Type|null
    {
        $classReflection = $scope->getClassReflection();
        if (! $classReflection instanceof ClassReflection) {
            return null;
        }

        $propertyName = $this->simpleNameResolver->getName($propertyFetch->name);
        if ($propertyName === null) {
            return null;
        }

        if (! $classReflection->hasProperty($propertyName)) {
            return null;
        }

        $propertyReflection = $classReflection->getProperty($propertyName, $scope);

        if ($propertyReflection instanceof PhpPropertyReflection) {
            $nativePropertyType = $propertyReflection->getNativeType();
        } else {
            return null;
        }

        // is known type → return it
        if (! $nativePropertyType instanceof MixedType) {
            return $nativePropertyType;
        }

        // detect default value - is null default null or no default value? :)
        $propertyDefaultValue = $this->resolvePropertyDefaultValue($classReflection, $propertyName);
        if ($propertyDefaultValue === null) {
            return null;
        }

        return $this->resolveScalarValueToType($propertyDefaultValue);
    }

    private function resolveScalarValueToType(
        mixed $propertyDefaultValue
    ): ArrayType|BooleanType|FloatType|IntegerType|StringType {
        if ($propertyDefaultValue === []) {
            return new ArrayType(new MixedType(), new MixedType());
        }

        if (\is_string($propertyDefaultValue)) {
            return new StringType();
        }

        if (\is_bool($propertyDefaultValue)) {
            return new BooleanType();
        }

        if (\is_int($propertyDefaultValue)) {
            return new IntegerType();
        }

        if (\is_float($propertyDefaultValue)) {
            return new FloatType();
        }

        throw new UnknownValueTypeException(\get_debug_type($propertyDefaultValue));
    }

    private function resolvePropertyDefaultValue(ClassReflection $classReflection, string $propertyFetchName): mixed
    {
        $nativeReflection = $classReflection->getNativeReflection();
        $reflectionProperty = $nativeReflection->getProperty($propertyFetchName);

        return $reflectionProperty->getDefaultValue();
    }
}
