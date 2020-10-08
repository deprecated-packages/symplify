<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use DateTimeImmutable;
use DateTimeInterface;
use Nette\Utils\DateTime;
use ReflectionParameter;
use ReflectionType;
use Symplify\EasyHydrator\Exception\MissingConstructorException;
use Symplify\PackageBuilder\Strings\StringFormatConverter;
use ReflectionClass;

final class ValueResolver
{
    /**
     * @var StringFormatConverter
     */
    private $stringFormatConverter;

    public function __construct(StringFormatConverter $stringFormatConverter)
    {
        $this->stringFormatConverter = $stringFormatConverter;
    }

    /**
     * @return array<int, mixed>
     */
    public function resolveClassConstructorValues(string $class, array $data): array
    {
        $arguments = [];

        $parameterReflections = $this->getConstructorParameterReflections($class);
        foreach ($parameterReflections as $parameterReflection) {
            $arguments[] = $this->resolveValue($data, $parameterReflection);
        }

        return $arguments;
    }

    private function resolveValue(array $data, ReflectionParameter $reflectionParameter)
    {
        $propertyKey = $reflectionParameter->name;
        $underscoreKey = $this->stringFormatConverter->camelCaseToUnderscore($reflectionParameter->name);

        $value = $data[$propertyKey] ?? $data[$underscoreKey] ?? '';

        return $this->retypeValue($reflectionParameter, $value);
    }

    /**
     * @return bool|int|string|mixed
     */
    private function retypeValue(ReflectionParameter $reflectionParameter, $value)
    {
        if ($this->isReflectionParameterOfType($reflectionParameter, DateTimeImmutable::class)) {
            return DateTimeImmutable::createFromMutable(DateTime::from($value));
        }

        if ($this->isReflectionParameterOfType($reflectionParameter, DateTimeInterface::class)) {
            return DateTime::from($value);
        }

        $parameterType = $reflectionParameter->getType();

        if ($parameterType !== null) {
            $parameterTypeName = $parameterType->getName();

            switch ($parameterTypeName) {
                case 'string':
                    return (string) $value;
                case 'bool':
                    return (bool) $value;
                case 'int':
                    return (int) $value;
                case 'array': // TODO: add test with generics to make sure reflection returns array
                    $newClassName = null; // @TODO: get class name (regex? phpstan?) from docbloc and then expand it by nette reflection

                    if ($newClassName === null) {
                        break;
                    }

                    $values = [];
                    foreach ($value as $sub) {
                        $resolveClassConstructorValues = $this->resolveClassConstructorValues($newClassName, $sub);

                        $values[] = $newClassName(...$resolveClassConstructorValues);
                    }
                    return $values;
                default:
                    if (class_exists($parameterTypeName)) {
                        $resolveClassConstructorValues = $this->resolveClassConstructorValues($parameterTypeName, $value);

                        return new $parameterTypeName(...$resolveClassConstructorValues);
                    }
            }
        }

        return $value;
    }

    private function isReflectionParameterOfType(ReflectionParameter $reflectionParameter, string $class): bool
    {
        $parameterType = $reflectionParameter->getType();
        if ($parameterType === null) {
            return false;
        }

        /** @var ReflectionType $parameterType */
        $parameterTypeName = method_exists(
            $parameterType,
            'getName'
        ) ? $parameterType->getName() : (string) $parameterType;

        return is_a($parameterTypeName, $class, true);
    }

    /**
     * @return ReflectionParameter[]
     */
    private function getConstructorParameterReflections(string $class): array
    {
        $reflectionClass = new ReflectionClass($class);

        $constructorReflectionMethod = $reflectionClass->getConstructor();
        if ($constructorReflectionMethod === null) {
            throw new MissingConstructorException(sprintf('Hydrated class "%s" is missing constructor.', $class));
        }

        return $constructorReflectionMethod->getParameters();
    }
}
