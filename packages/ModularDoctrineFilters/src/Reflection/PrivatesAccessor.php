<?php declare(strict_types=1);

namespace Symplify\ModularDoctrineFilters\Reflection;

use ReflectionClass;
use ReflectionProperty;

final class PrivatesAccessor
{
    public static function accessClassProperty(object $object, string $propertyName): ReflectionProperty
    {
        $reflectionProperty = (new ReflectionClass($object))->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty;
    }
}
