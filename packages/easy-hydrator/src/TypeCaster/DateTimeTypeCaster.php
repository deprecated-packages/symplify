<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\TypeCaster;

use DateTimeImmutable;
use DateTimeInterface;
use Nette\Utils\DateTime;
use ReflectionParameter;
use Symplify\EasyHydrator\ClassConstructorValuesResolver;
use Symplify\EasyHydrator\Contract\TypeCasterInterface;
use Symplify\EasyHydrator\ParameterTypeRecognizer;

final class DateTimeTypeCaster implements TypeCasterInterface
{
    public function __construct(
        private ParameterTypeRecognizer $parameterTypeRecognizer
    ) {
    }

    public function isSupported(ReflectionParameter $reflectionParameter): bool
    {
        return $this->parameterTypeRecognizer->isParameterOfClass($reflectionParameter, DateTimeInterface::class);
    }

    /**
     * @return DateTimeImmutable|DateTime|null
     */
    public function retype(
        $value,
        ReflectionParameter $reflectionParameter,
        ClassConstructorValuesResolver $classConstructorValuesResolver
    ) {
        if ($value === null && $reflectionParameter->allowsNull()) {
            return null;
        }

        $dateTime = DateTime::from($value);
        $class = $this->parameterTypeRecognizer->getType($reflectionParameter);

        if ($class === DateTimeImmutable::class) {
            return DateTimeImmutable::createFromMutable($dateTime);
        }

        return $dateTime;
    }

    public function getPriority(): int
    {
        return 1;
    }
}
