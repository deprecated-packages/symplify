<?php declare (strict_types=1);

namespace Symplify\EasyHydrator\TypeCaster;

use ReflectionParameter;
use Symplify\EasyHydrator\ClassConstructorValuesResolver;
use Symplify\EasyHydrator\TypeRecognizer;

final class ScalarTypeCaster implements TypeCasterInterface
{
    private $typeRecognizer;


    public function __construct(TypeRecognizer $typeRecognizer)
    {
        $this->typeRecognizer = $typeRecognizer;
    }


    public function isSupported(ReflectionParameter $reflectionParameter): bool
    {
        $type = $this->typeRecognizer->getParameterType($reflectionParameter);

        return in_array($type, ['string', 'bool', 'int'], true);
    }


    public function retype($value, ReflectionParameter $reflectionParameter, ClassConstructorValuesResolver $classConstructorValuesResolver)
    {
        $type = $this->typeRecognizer->getParameterType($reflectionParameter);

        if ($type === 'string') {
            return (string) $value;
        }

        if ($type === 'bool') {
            return (bool) $value;
        }

        if ($type === 'int') {
            return (int) $type;
        }

        return $value;
    }


    public function getPriority(): int
    {
        return 10;
    }
}
