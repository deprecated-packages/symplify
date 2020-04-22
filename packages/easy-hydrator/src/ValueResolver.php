<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

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
        if ($this->isDateTimeType($reflectionParameter)) {
            return DateTime::from($value);
        }

        $parameterType = (string) $reflectionParameter->getType();
        if ($parameterType === 'string') {
            return (string) $value;
        }

        if ($parameterType === 'bool') {
            return (bool) $value;
        }

        if ($parameterType === 'int') {
            return (int) $value;
        }

        return $value;
    }

    private function isDateTimeType(ReflectionParameter $reflectionParameter): bool
    {
        $parameterType = $reflectionParameter->getType();
        if ($parameterType === null) {
            return false;
        }

        /** @var ReflectionType $parameterType */
        $parameterTypeName = $parameterType->getName();

        return is_a($parameterTypeName, DateTimeInterface::class, true);
    }
}
