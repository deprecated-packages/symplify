<?php declare(strict_types=1);

namespace Symplify\EasyHydrator;

use ReflectionClass;
use ReflectionMethod;
use Symplify\EasyHydrator\Exception\MissingConstructorException;
use Symplify\EasyHydrator\ParameterValueGetter\ParameterValueGetterInterface;

final class ClassConstructorValuesResolver
{
    /**
     * @var TypeCastersCollector
     */
    private $typeCastersCollector;

    private $parameterValueGetter;

    public function __construct(
        TypeCastersCollector $typeCastersCollector,
        ParameterValueGetterInterface $parameterValueGetter
    ) {
        $this->typeCastersCollector = $typeCastersCollector;
        $this->parameterValueGetter = $parameterValueGetter;
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
            $value = $this->parameterValueGetter->getValue($parameterReflection, $data);

            $arguments[] = $this->typeCastersCollector->retype($value, $parameterReflection, $this);
        }

        return $arguments;
    }

    private function getConstructorMethodReflection(string $class): ReflectionMethod
    {
        $reflectionClass = new ReflectionClass($class);

        $constructorReflectionMethod = $reflectionClass->getConstructor();
        if ($constructorReflectionMethod === null) {
            throw new MissingConstructorException(sprintf('Hydrated class "%s" is missing constructor.', $class));
        }

        return $constructorReflectionMethod;
    }
}
