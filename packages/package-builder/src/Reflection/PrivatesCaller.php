<?php

declare(strict_types=1);

namespace Symplify\PackageBuilder\Reflection;

use ReflectionClass;
use ReflectionMethod;

/**
 * @see \Symplify\PackageBuilder\Tests\Reflection\PrivatesCallerTest
 * @see \Symplify\PackageBuilder\Tests\Reflection\PrivatesCallerTest*/
final class PrivatesCaller
{
    /**
     * @param mixed[] $arguments
     */
    public function callPrivateMethod(object | string $object, string $methodName, array $arguments): mixed
    {
        if (is_string($object)) {
            $reflectionClass = new ReflectionClass($object);
            $object = $reflectionClass->newInstanceWithoutConstructor();
        }

        $methodReflection = $this->createAccessibleMethodReflection($object, $methodName);

        return $methodReflection->invokeArgs($object, $arguments);
    }

    public function callPrivateMethodWithReference(object | string $object, string $methodName, mixed $argument): mixed
    {
        if (is_string($object)) {
            $reflectionClass = new ReflectionClass($object);
            $object = $reflectionClass->newInstanceWithoutConstructor();
        }

        $methodReflection = $this->createAccessibleMethodReflection($object, $methodName);
        $methodReflection->invokeArgs($object, [&$argument]);

        return $argument;
    }

    private function createAccessibleMethodReflection(object $object, string $methodName): ReflectionMethod
    {
        $reflectionMethod = new ReflectionMethod($object::class, $methodName);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod;
    }
}
