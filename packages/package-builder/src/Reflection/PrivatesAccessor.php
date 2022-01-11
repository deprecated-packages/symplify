<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Reflection;

use ReflectionProperty;
use Symplify\PackageBuilder\Exception\InvalidPrivatePropertyTypeException;
use Symplify\PackageBuilder\Exception\MissingPrivatePropertyException;

/**
 * @api
 * @see \Symplify\PackageBuilder\Tests\Reflection\PrivatesAccessorTest
 */
final class PrivatesAccessor
{
    /**
     * @template T as object
     *
     * @param class-string<T> $valueClassName
     * @return T
     */
    public function getPrivatePropertyOfClass(object $object, string $propertyName, string $valueClassName): object
    {
        $value = $this->getPrivateProperty($object, $propertyName);
        if ($value instanceof $valueClassName) {
            return $value;
        }

        $errorMessage = sprintf('The type "%s" is required, but "%s" type given', $valueClassName, get_class($value));
        throw new InvalidPrivatePropertyTypeException($errorMessage);
    }

    /**
     * @return mixed
     */
    public function getPrivateProperty(object $object, string $propertyName)
    {
        $propertyReflection = $this->resolvePropertyReflection($object, $propertyName);
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($object);
    }

    /**
     * @template T
     *
     * @param class-string<T> $valueClassName
     * @param T $value
     */
    public function setPrivatePropertyOfClass(
        object $object,
        string $propertyName,
        mixed $value,
        string $valueClassName
    ): void {
        if ($value instanceof $valueClassName) {
            $this->setPrivateProperty($object, $propertyName, $value);
            return;
        }

        $errorMessage = sprintf(
            'The type "%s" is required, but "%s" type given',
            $valueClassName,
            get_class($value)
        );
        throw new InvalidPrivatePropertyTypeException($errorMessage);
    }

    public function setPrivateProperty(object $object, string $propertyName, mixed $value): void
    {
        $propertyReflection = $this->resolvePropertyReflection($object, $propertyName);
        $propertyReflection->setAccessible(true);

        $propertyReflection->setValue($object, $value);
    }

    private function resolvePropertyReflection(object $object, string $propertyName): ReflectionProperty
    {
        if (property_exists($object, $propertyName)) {
            return new ReflectionProperty($object, $propertyName);
        }

        $parentClass = get_parent_class($object);
        if ($parentClass !== false) {
            return new ReflectionProperty($parentClass, $propertyName);
        }

        $errorMessage = sprintf('Property "$%s" was not found in "%s" class', $propertyName, $object::class);
        throw new MissingPrivatePropertyException($errorMessage);
    }
}
