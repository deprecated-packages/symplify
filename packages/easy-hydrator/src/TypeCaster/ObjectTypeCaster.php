<?php declare (strict_types=1);

namespace Symplify\EasyHydrator\TypeCaster;

use ReflectionParameter;
use Symplify\EasyHydrator\ClassConstructorValuesResolver;
use Symplify\EasyHydrator\TypeRecognizer;

final class ObjectTypeCaster implements TypeCasterInterface
{
    private $typeRecognizer;


    public function __construct(TypeRecognizer $typeRecognizer)
    {
        $this->typeRecognizer = $typeRecognizer;
    }


    public function isSupported(ReflectionParameter $reflectionParameter): bool
    {
        $class = $this->typeRecognizer->getParameterClass($reflectionParameter);

        if ($class === null) {
            return false;
        }

        return class_exists($class);
    }


    public function retype($value, ReflectionParameter $reflectionParameter, ClassConstructorValuesResolver $classConstructorValuesResolver)
    {
        $className = $this->typeRecognizer->getParameterClass($reflectionParameter);

        $constructorValues = $classConstructorValuesResolver->resolve($className, $value);

        return new $className(...$constructorValues);
    }


    public function getPriority(): int
    {
        return 5;
    }
}
