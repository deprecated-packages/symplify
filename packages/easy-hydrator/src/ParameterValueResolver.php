<?php declare(strict_types=1);

namespace Symplify\EasyHydrator;

use ReflectionParameter;
use Symplify\EasyHydrator\Exception\MissingDataException;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

final class ParameterValueResolver
{
    /**
     * @var StringFormatConverter
     */
    private $stringFormatConverter;

    public function __construct(StringFormatConverter $stringFormatConverter)
    {
        $this->stringFormatConverter = $stringFormatConverter;
    }

    /**
     * @param mixed[] $data
     * @return mixed
     */
    public function getValue(ReflectionParameter $reflectionParameter, array $data)
    {
        $parameterName = $reflectionParameter->name;

        $underscoreParameterName = $this->stringFormatConverter->camelCaseToUnderscore($parameterName);

        if (array_key_exists($parameterName, $data)) {
            return $data[$parameterName];
        }

        if (array_key_exists($underscoreParameterName, $data)) {
            return $data[$underscoreParameterName];
        }

        if ($reflectionParameter->isDefaultValueAvailable()) {
            return $reflectionParameter->getDefaultValue();
        }

        $declaringClassReflection = $reflectionParameter->getDeclaringClass();

        throw new MissingDataException(sprintf(
            'Missing data of "$%s" parameter for hydrated class "%s" __construct method.',
            $parameterName,
            $declaringClassReflection !== null ? $declaringClassReflection->getName() : ''
        ));
    }
}
