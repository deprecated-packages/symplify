<?php declare(strict_types=1);

namespace Symplify\EasyHydrator\ParameterValueGetter;

use ReflectionParameter;

interface ParameterValueGetterInterface
{
    /**
     * @param mixed[] $data
     * @return mixed
     */
    public function getValue(ReflectionParameter $reflectionParameter, array $data);
}
