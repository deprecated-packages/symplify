<?php declare(strict_types=1);

namespace Symplify\PackageBuilder\Reflection;

use ReflectionClass;
use ReflectionMethod;

final class PrivatesCaller
{
    /**
     * @param object|string $object
     * @param mixed[] $arguments
     * @return mixed
     */
    public function callPrivateMethod($object, string $methodName, ...$arguments)
    {
        if (is_string($object)) {
            $object = (new ReflectionClass($object))->newInstanceWithoutConstructor();
        }

        $methodReflection = $this->createAccessibleMethodReflection($object, $methodName);

        return $methodReflection->invoke($object, ...$arguments);
    }

    /**
     * @param object|string $object
     * @param mixed $argument
     * @return mixed
     */
    public function callPrivateMethodWithReference($object, string $methodName, $argument)
    {
        if (is_string($object)) {
            $object = (new ReflectionClass($object))->newInstanceWithoutConstructor();
        }

        $methodReflection = $this->createAccessibleMethodReflection($object, $methodName);

        $methodReflection->invokeArgs($object, [&$argument]);

        return $argument;
    }

    /**
     * @param object $object
     */
    private function createAccessibleMethodReflection($object, string $methodName): ReflectionMethod
    {
        $methodReflection = new ReflectionMethod(get_class($object), $methodName);
        $methodReflection->setAccessible(true);

        return $methodReflection;
    }
}
