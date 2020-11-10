<?php

declare(strict_types=1);

namespace Symplify\PHPStanPHPConfig\Reflection;

use ReflectionClass;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

final class ConstructorParameterNameResolver
{
    public function resolveFromClassAndArgumentPosition(string $serviceClass, int $argumentPosition): string
    {
        $reflectionClass = new ReflectionClass($serviceClass);
        $constructorReflection = $reflectionClass->getConstructor();
        if ($constructorReflection === null) {
            throw new ShouldNotHappenException();
        }

        foreach ($constructorReflection->getParameters() as $key => $parameterReflection) {
            if ($key !== $argumentPosition) {
                continue;
            }

            return '$' . $parameterReflection->name;
        }

        throw new ShouldNotHappenException();
    }
}
