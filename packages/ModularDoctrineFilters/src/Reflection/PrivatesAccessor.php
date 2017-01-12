<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Reflection;

use ReflectionClass;
use ReflectionProperty;

final class PrivatesAccessor
{
    /**
     * @param $object
     * @param string $propertyName
     */
    public static function accessClassProperty($object, string $propertyName) : ReflectionProperty
    {
        $reflectionProperty = (new ReflectionClass($object))->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty;
    }
}
