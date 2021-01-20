<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use ReflectionClass;
use ReflectionMethod;
use Symplify\EasyHydrator\Exception\MissingConstructorException;

final class ClassConstructorValuesResolver
{
    /**
     * @var TypeCastersCollector
     */
    private $typeCastersCollector;

    /**
     * @var ParameterValueResolver
     */
    private $parameterValueResolver;

    public function __construct(
        TypeCastersCollector $typeCastersCollector,
        ParameterValueResolver $parameterValueResolver
    ) {
        $this->typeCastersCollector = $typeCastersCollector;
        $this->parameterValueResolver = $parameterValueResolver;
    }

    /**
     * @return array<int, mixed>
     */
    public function resolve(string $class, array $data): array
    {
        $arguments = [];

        $constructorMethodReflection = $this->getConstructorMethodReflection($class);
        $parameterReflections = $constructorMethodReflection->getParameters();

        foreach ($parameterReflections as $parameterReflection) {
            $value = $this->parameterValueResolver->getValue($parameterReflection, $data);

            $arguments[] = $this->typeCastersCollector->retype($value, $parameterReflection, $this);
        }

        return $arguments;
    }

    private function getConstructorMethodReflection(string $class): ReflectionMethod
    {
        $reflectionClass = new ReflectionClass($class);

        $constructorReflectionMethod = $reflectionClass->getConstructor();
        if (! $constructorReflectionMethod instanceof ReflectionMethod) {
            throw new MissingConstructorException(sprintf('Hydrated class "%s" is missing constructor.', $class));
        }

        return $constructorReflectionMethod;
    }
}
