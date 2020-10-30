<?php declare(strict_types=1);

namespace Symplify\EasyHydrator\TypeCaster;

use ReflectionParameter;
use Symplify\EasyHydrator\ClassConstructorValuesResolver;
use Symplify\EasyHydrator\ParameterTypeRecognizer;

final class ArrayTypeCaster implements TypeCasterInterface
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
        $type = $this->parameterTypeRecognizer->getType($reflectionParameter);

        return $type === 'array';
    }

    public function retype(
        $value,
        ReflectionParameter $reflectionParameter,
        ClassConstructorValuesResolver $classConstructorValuesResolver
    ) {
        $type = $this->parameterTypeRecognizer->getTypeFromDocBlock($reflectionParameter);

        if ($value === null && $reflectionParameter->allowsNull()) {
            return null;
        }

        return array_map(static function ($value) use ($type) {
            if ($type === 'string') {
                return (string) $value;
            }

            if ($type === 'bool') {
                return (bool) $value;
            }

            if ($type === 'int') {
                return (int) $value;
            }

            if ($type === 'float') {
                return (float) $value;
            }

            return $value;
        }, $value);
    }

    public function getPriority(): int
    {
        return 8;
    }
}
