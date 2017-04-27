<?php declare(strict_types=1);

namespace Symplify\DefaultAutowire\DependencyInjection;

use ReflectionMethod;
use ReflectionParameter;
use Symfony\Component\DependencyInjection\Definition;

final class MethodAnalyzer
{
    public function hasMethodWithMissingArgumentTypehints(
        ReflectionMethod $methodReflection,
        Definition $definition
    ): bool {
        if (! $this->hasMethodArguments($methodReflection)) {
            return false;
        }

        if ($this->areAllMethodArgumentsRequired($methodReflection, $definition)) {
            return false;
        }

        if (! $this->areArgumentsMissingTypehints($methodReflection, $definition)) {
            return false;
        }

        return true;
    }

    private function areArgumentsMissingTypehints(ReflectionMethod $methodReflection, Definition $definition): bool
    {
        $arguments = $definition->getArguments();

        foreach ($methodReflection->getParameters() as $position => $parameterReflection) {
            if ($this->shouldSkipParameter($parameterReflection, $arguments, $position)) {
                continue;
            }

            $parameterType = $parameterReflection->getType();
            if (! $parameterType || $parameterType->isBuiltin()) {
                return false;
            }

            if (! $parameterType->allowsNull()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed[] $arguments
     */
    private function shouldSkipParameter(
        ReflectionParameter $parameterReflection,
        array $arguments,
        int $position
    ): bool {
        if (isset($arguments[$position])) {
            return true;
        }

        if ($parameterReflection->isDefaultValueAvailable()) {
            return true;
        }

        return false;
    }

    private function hasMethodArguments(ReflectionMethod $methodReflection): bool
    {
        return $methodReflection->getNumberOfParameters() !== 0;
    }

    private function areAllMethodArgumentsRequired(ReflectionMethod $methodReflection, Definition $definition): bool
    {
        $methodArgumentsCount = count($definition->getArguments());
        $methodRequiredArgumentsCount = $methodReflection->getNumberOfRequiredParameters();

        return $methodArgumentsCount === $methodRequiredArgumentsCount;
    }
}
