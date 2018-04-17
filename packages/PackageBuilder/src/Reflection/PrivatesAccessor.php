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
        $propertyReflection = new ReflectionProperty(get_class($object), $propertyName);
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
