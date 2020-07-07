<?php

namespace Symplify\CodingStandard\Tests\Fixer\Commenting\ParamReturnAndVarTagMalformsFixer\FixturePhp74;

class SkipNestedCall
{
    /**
     * @param mixed[] $value
     *
     * @return mixed[]
     */
    public static function typeCastAll(string $type, array $value): array
    {
        return array_map(
            /**
             * @param mixed $item
             *
             * @return mixed
             */
            static fn($item) => self::typeCast($type, $item),
            $value
        );
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public static function typeCast(string $type, $value)
    {
        return $value;
    }
}
