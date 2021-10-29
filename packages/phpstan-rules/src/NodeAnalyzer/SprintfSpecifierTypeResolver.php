<?php

declare(strict_types=1);

namespace Symplify\PHPStanRules\NodeAnalyzer;

use PHPStan\Type\FloatType;
use PHPStan\Type\IntegerType;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

final class SprintfSpecifierTypeResolver
{
    /**
     * @var array<string, array<class-string<Type>>>
     *
     * @see https://www.php.net/manual/en/function.sprintf.php > "Specifiers"
     */
    private const MASK_TO_STATIC_TYPES_MAP = [
        '%s' => [StringType::class],
        '%d' => [IntegerType::class, FloatType::class],
        '%f' => [FloatType::class],
        // @todo if needed
    ];

    /**
     * @param string[] $specifiers
     * @return array<array<Type>>
     *
     * @see https://www.php.net/manual/en/function.sprintf.php > "Specifiers"
     */
    public function resolveFromSpecifiers(array $specifiers): array
    {
        $expectedTypes = [];

        foreach ($specifiers as $specifier) {
            $types = [];

            if (isset(self::MASK_TO_STATIC_TYPES_MAP[$specifier])) {
                $typeClasses = self::MASK_TO_STATIC_TYPES_MAP[$specifier];
                foreach ($typeClasses as $typeClass) {
                    $types[] = new $typeClass();
                }
            } else {
                $types = [new MixedType()];
            }

            $expectedTypes[] = $types;
        }

        return $expectedTypes;
    }
}
