<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Reflection;

use ReflectionProperty;

final class PrivatesAccessor
{
    /**
     * @param object $object
     * @return mixed
     */
    public function getPrivateProperty($object, string $propertyName)
    {
        if (property_exists($object, $propertyName)) {
            $propertyReflection = new ReflectionProperty($object, $propertyName);
        } else {
            $propertyReflection = new ReflectionProperty(get_parent_class($object), $propertyName);
        }
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($object);
    }

    /**
     * @param object $object
     * @param mixed $value
     */
    public function setPrivateProperty($object, string $propertyName, $value): void
    {
        $propertyReflection = new ReflectionProperty(get_class($object), $propertyName);
        $propertyReflection->setAccessible(true);

        $propertyReflection->setValue($object, $value);
    }
}
