<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use DateTimeImmutable;
use DateTimeInterface;
use Nette\Utils\DateTime;
use ReflectionParameter;
use ReflectionType;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

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

    public function resolveValue(array $data, ReflectionParameter $reflectionParameter)
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
        if ($this->isDateTimeType($reflectionParameter, DateTimeImmutable::class)) {
            return DateTimeImmutable::createFromMutable(DateTime::from($value));
        }

        if ($this->isDateTimeType($reflectionParameter, DateTimeInterface::class)) {
            return DateTime::from($value);
        }

        $parameterType = $reflectionParameter->getType();

        if ($parameterType !== null) {
            switch ($parameterType->getName()) {
                case 'string':
                    return (string) $value;
                case 'bool':
                    return (bool) $value;
                case 'int':
                    return (int) $value;
            }
        }

        return $value;
    }

    private function isDateTimeType(ReflectionParameter $reflectionParameter, string $class): bool
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
}
