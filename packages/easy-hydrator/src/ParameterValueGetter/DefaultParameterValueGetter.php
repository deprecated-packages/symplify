<?php declare(strict_types=1);

namespace Symplify\EasyHydrator\ParameterValueGetter;

use ReflectionParameter;
use Symplify\PackageBuilder\Strings\StringFormatConverter;

final class DefaultParameterValueGetter implements ParameterValueGetterInterface
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

        return null;
    }
}
