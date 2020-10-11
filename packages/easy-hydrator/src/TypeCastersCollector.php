<?php declare (strict_types=1);

namespace Symplify\EasyHydrator;

use ReflectionParameter;

final class TypeCastersCollector implements TypeCaster
{
    /**
     * @var TypeCaster[]
     */
    private $typeCasters;

    /**
     * @param TypeCaster[] $typeCasters
     */
    public function __construct(array $typeCasters)
    {
        $this->typeCasters = $typeCasters;
    }

    public function isSupported(string $type): bool
    {
        return true;
    }

    public function retype($value, ReflectionParameter $reflectionParameter)
    {
        $type = ''; // @TODO

        foreach ($this->typeCasters as $typeCaster) {
            if ($typeCaster->isSupported($type)) {
                return $typeCaster->retype($value, $reflectionParameter);
            }
        }

        return $value;
    }
}
