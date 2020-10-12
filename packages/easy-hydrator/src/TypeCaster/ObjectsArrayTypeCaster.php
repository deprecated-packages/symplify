<?php declare (strict_types=1);

namespace Symplify\EasyHydrator\TypeCaster;

use ReflectionParameter;
use Symplify\EasyHydrator\ClassConstructorValuesResolver;
use Symplify\EasyHydrator\TypeRecognizer;

final class ObjectsArrayTypeCaster implements TypeCasterInterface
{
    private $objectTypeCaster;

    private $typeRecognizer;

    public function __construct(ObjectTypeCaster $objectTypeCaster, TypeRecognizer $typeRecognizer)
    {
        $this->objectTypeCaster = $objectTypeCaster;
        $this->typeRecognizer = $typeRecognizer;
    }

    public function isSupported(ReflectionParameter $reflectionParameter): bool
    {
        $type = $this->typeRecognizer->getParameterType($reflectionParameter);

        if ($type !== 'array') {
            return false;
        }

        $newClass = $this->typeRecognizer->getParameterClass($reflectionParameter);

        if ($newClass === null) {
            return false;
        }

        return class_exists($newClass);
    }

    public function retype($values, ReflectionParameter $reflectionParameter, ClassConstructorValuesResolver $classConstructorValuesResolver)
    {
        $objects = [];

        foreach ($values as $value) {
            $objects[] = $this->objectTypeCaster->retype($value, $reflectionParameter, $classConstructorValuesResolver);
        }

        return $objects;
    }

    public function getPriority(): int
    {
        return 5;
    }
}
