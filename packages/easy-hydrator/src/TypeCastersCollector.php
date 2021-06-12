<?php

declare(strict_types=1);

namespace Symplify\EasyHydrator;

use ReflectionParameter;
use Symplify\EasyHydrator\Contract\TypeCasterInterface;

final class TypeCastersCollector
{
    /**
     * @var TypeCasterInterface[]
     */
    private array $typeCasters = [];

    /**
     * @param TypeCasterInterface[] $typeCasters
     */
    public function __construct(array $typeCasters)
    {
        $this->typeCasters = $this->sortCastersByPriority($typeCasters);
    }

    /**
     * @return mixed
     */
    public function retype(
        $value,
        ReflectionParameter $reflectionParameter,
        ClassConstructorValuesResolver $classConstructorValuesResolver
    ) {
        foreach ($this->typeCasters as $typeCaster) {
            if ($typeCaster->isSupported($reflectionParameter)) {
                return $typeCaster->retype($value, $reflectionParameter, $classConstructorValuesResolver);
            }
        }

        return $value;
    }

    /**
     * @param TypeCasterInterface[] $typeCasters
     * @return TypeCasterInterface[]
     */
    private function sortCastersByPriority(array $typeCasters): array
    {
        usort(
            $typeCasters,
            static fn (TypeCasterInterface $firstTypeCaster, TypeCasterInterface $secondTypeCaster): int => $firstTypeCaster->getPriority() <=> $secondTypeCaster->getPriority()
        );

        return $typeCasters;
    }
}
