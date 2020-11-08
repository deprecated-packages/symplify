<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator\Contract;

use ReflectionParameter;
use Symplify\EasyHydrator\ClassConstructorValuesResolver;

interface TypeCasterInterface
{
    public function getPriority(): int;

    public function isSupported(ReflectionParameter $reflectionParameter): bool;

    /**
     * @param mixed $value
     * @return mixed
     */
    public function retype(
        $value,
        ReflectionParameter $reflectionParameter,
        ClassConstructorValuesResolver $classConstructorValuesResolver
    );
}
