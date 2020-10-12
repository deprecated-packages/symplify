<?php declare(strict_types=1);

namespace Symplify\EasyHydrator\TypeCaster;

use DateTimeImmutable;
use DateTimeInterface;
use Nette\Utils\DateTime;
use ReflectionParameter;
use Symplify\EasyHydrator\ClassConstructorValuesResolver;
use Symplify\EasyHydrator\ParameterTypeRecognizer;

final class DateTimeTypeCaster implements TypeCasterInterface
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
        return $this->parameterTypeRecognizer->isParameterOfClass($reflectionParameter, DateTimeInterface::class);
    }

    /**
     * @return DateTimeImmutable|DateTime
     */
    public function retype(
        $value,
        ReflectionParameter $reflectionParameter,
        ClassConstructorValuesResolver $classConstructorValuesResolver
    ) {
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
