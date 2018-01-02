<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Reflection;

use ReflectionMethod;

final class PrivatesCaller
{
    /**
     * @param object $object
     * @return mixed
     */
    public function callPrivateMethod($object, string $methodName, ...$arguments)
    {
        $methodReflection = new ReflectionMethod(get_class($object), $methodName);
        $methodReflection->setAccessible(true);

        return $methodReflection->invoke($object, ...$arguments);
    }
}
