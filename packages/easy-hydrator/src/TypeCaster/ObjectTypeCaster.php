<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\TypeCaster;

use ReflectionParameter;
use Symplify\EasyHydrator\ClassConstructorValuesResolver;
use Symplify\EasyHydrator\Contract\TypeCasterInterface;
use Symplify\EasyHydrator\ParameterTypeRecognizer;

final class ObjectTypeCaster implements TypeCasterInterface
{
    /**
     * @var ParameterTypeRecognizer
     */
    private $parameterTypeRecognizer;

    public function __construct(ParameterTypeRecognizer $parameterTypeRecognizer)
    {
        $this->parameterTypeRecognizer = $parameterTypeRecognizer;
    }

    public function isSupported(ReflectionParameter $reflectionParameter): bool
    {
        $className = $this->getClassName($reflectionParameter);

        if ($className === null) {
            return false;
        }

        return class_exists($className);
    }

    /**
     * @return mixed|mixed[]
     */
    public function retype(
        $value,
        ReflectionParameter $reflectionParameter,
        ClassConstructorValuesResolver $classConstructorValuesResolver
    ) {
        $className = $this->getClassName($reflectionParameter);

        if ($className === null) {
            return $value;
        }

        if ($value === null && $reflectionParameter->allowsNull()) {
            return null;
        }

        if (! $this->parameterTypeRecognizer->isArray($reflectionParameter)) {
            return $this->createObject($className, $value, $classConstructorValuesResolver);
        }

        return array_map(function ($objectData) use ($className, $classConstructorValuesResolver) {
            return $this->createObject($className, $objectData, $classConstructorValuesResolver);
        }, $value);
    }

    public function getPriority(): int
    {
        return 5;
    }

    /**
     * @param mixed $data
     * @return object|mixed
     */
    private function createObject(
        string $className,
        $data,
        ClassConstructorValuesResolver $classConstructorValuesResolver
    ) {
        if (is_a($data, $className)) {
            return $data;
        }

        $constructorValues = $classConstructorValuesResolver->resolve($className, $data);

        return new $className(...$constructorValues);
    }

    private function getClassName(ReflectionParameter $reflectionParameter): ?string
    {
        if ($this->parameterTypeRecognizer->isArray($reflectionParameter)) {
            return $this->parameterTypeRecognizer->getTypeFromDocBlock($reflectionParameter);
        }

        return $this->parameterTypeRecognizer->getType($reflectionParameter);
    }
}
