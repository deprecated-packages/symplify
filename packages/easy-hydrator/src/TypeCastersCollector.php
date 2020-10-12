<?php declare(strict_types=1);

namespace Symplify\EasyHydrator;

use ReflectionParameter;
use Symplify\EasyHydrator\TypeCaster\TypeCasterInterface;

final class TypeCastersCollector
{
    /**
     * @var TypeCasterInterface[]
     */
    private $typeCasters = [];

    /**
     * @param TypeCasterInterface[] $typeCasters
     */
    public function __construct(array $typeCasters)
    {
        $this->sortCastersByPriority(...$typeCasters);

        $this->typeCasters = $typeCasters;
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

    private function sortCastersByPriority(TypeCasterInterface ...$typeCaster): void
    {
        usort($typeCaster, static function (TypeCasterInterface $a, TypeCasterInterface $b): int {
            return $a->getPriority() <=> $b->getPriority();
        });
    }
}
