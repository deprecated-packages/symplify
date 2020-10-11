<?php declare (strict_types=1);

namespace Symplify\EasyHydrator;

use ReflectionParameter;

interface TypeCaster
{
    public function isSupported(string $type): bool;

    /**
     * @param mixed $value
     * @return mixed
     */
    public function retype($value, ReflectionParameter $reflectionParameter);
}
