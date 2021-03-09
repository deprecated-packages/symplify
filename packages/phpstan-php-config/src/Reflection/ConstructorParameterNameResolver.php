<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\Reflection;

use ReflectionClass;
use ReflectionMethod;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ConstructorParameterNameResolver
{
    public function resolveFromClassAndArgumentPosition(string $serviceClass, int $argumentPosition): string
    {
        $reflectionClass = new ReflectionClass($serviceClass);

        $constructorReflectionMethod = $reflectionClass->getConstructor();
        if (! $constructorReflectionMethod instanceof ReflectionMethod) {
            throw new ShouldNotHappenException();
        }

        foreach ($constructorReflectionMethod->getParameters() as $key => $reflectionParameter) {
            if ($key !== $argumentPosition) {
                continue;
            }

            return '$' . $reflectionParameter->name;
        }

        throw new ShouldNotHappenException();
    }
}
