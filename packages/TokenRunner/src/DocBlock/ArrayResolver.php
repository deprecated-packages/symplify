<?php declare(strict_types=1);

namespace Symplify\TokenRunner\DocBlock;

use Nette\Utils\Strings;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Mixed_;

final class ArrayResolver
{
    public static function resolveArrayType(string $originalContent, Array_ $arrayType, string $tagName, ?string $propertyName = null): string
    {
        if ($arrayType->getValueType() instanceof Mixed_) {
            $matched = self::matchArrayOrMixedAnnotation($originalContent, $tagName, $propertyName);
            if ($matched) {
                return $matched['type'];
            }
        }

        return 'array';
    }

    /**
     * Matches:
     * - @param array $propertyName
     * - @param mixed[] $propertyName
     * - @return array
     * - @return mixed[]
     *
     * @return mixed[]|null
     */
    private static function matchArrayOrMixedAnnotation(
        string $originalContent,
        string $tagName,
        ?string $propertyName = null
    ): ?array {
        $mask = sprintf('@%s\s+(?<type>array|mixed\[\])', $tagName);
        if ($propertyName) {
            $mask .= sprintf('\s+\$%s', $propertyName);
        }

        return Strings::match($originalContent, '#' . $mask . '#');
    }
}
