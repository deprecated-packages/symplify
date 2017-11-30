<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Reflection;

use ReflectionClass;

final class PrivatesCaller
{
    /**
     * @param object $object
     * @return mixed
     */
    public function callPrivateMethod($object, string $methodName, ...$arguments)
    {
        $classReflection = new ReflectionClass(get_class($object));

        $methodReflection = $classReflection->getMethod($methodName);
        $methodReflection->setAccessible(true);

        return $methodReflection->invoke($object, ...$arguments);
    }
}
