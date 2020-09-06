<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Reflection;

use ReflectionProperty;

/**
 * @see \Symplify\PackageBuilder\Tests\Reflection\PrivatesAccessorTest
 */
final class PrivatesAccessor
{
    public function getPrivateProperty(object $object, string $propertyName)
    {
        if (property_exists($object, $propertyName)) {
            $propertyReflection = new ReflectionProperty($object, $propertyName);
        } else {
            $propertyReflection = new ReflectionProperty(get_parent_class($object), $propertyName);
        }
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($object);
    }

    public function setPrivateProperty(object $object, string $propertyName, $value): void
    {
        $propertyReflection = new ReflectionProperty(get_class($object), $propertyName);
        $propertyReflection->setAccessible(true);

        $propertyReflection->setValue($object, $value);
    }
}
