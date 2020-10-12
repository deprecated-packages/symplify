<?php declare (strict_types=1);

namespace Symplify\EasyHydrator\TypeCaster;

use DateTimeImmutable;
use DateTimeInterface;
use Nette\Utils\DateTime;
use ReflectionParameter;
use Symplify\EasyHydrator\ClassConstructorValuesResolver;
use Symplify\EasyHydrator\TypeRecognizer;

final class DateTimeTypeCaster implements TypeCasterInterface
{
    private $typeRecognizer;

    public function __construct(TypeRecognizer $typeRecognizer)
    {
        $this->typeRecognizer = $typeRecognizer;
    }

    public function isSupported(ReflectionParameter $reflectionParameter): bool
    {
        return $this->typeRecognizer->isParameterOfClass($reflectionParameter, DateTimeInterface::class);
    }

    public function retype($value, ReflectionParameter $reflectionParameter, ClassConstructorValuesResolver $classConstructorValuesResolver)
    {
        $dateTime = DateTime::from($value);
        $class = $this->typeRecognizer->getParameterClass($reflectionParameter);

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
